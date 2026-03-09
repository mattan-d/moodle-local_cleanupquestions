<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Scheduled task: nightly cleanup of all courses with limits and statistics report.
 *
 * @package    local_cleanupquestions
 * @copyright  CentricApp LTD (Dev Team) <dev@centricapp.co.il>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_cleanupquestions\task;

use core\task\scheduled_task;
use local_cleanupquestions\helper;
use core\message\message;
use core_user;

defined('MOODLE_INTERNAL') || die();

/**
 * Scheduled task that runs once per night, cleans up all courses with time/course limits, and sends a statistics report.
 */
class nightly_cleanup_all_task extends scheduled_task {

    /**
     * Get a descriptive name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('nightlycleanuptaskname', 'local_cleanupquestions');
    }

    /**
     * Execute the nightly cleanup: process courses with limits, then send report to admins.
     */
    public function execute() {
        global $DB;

        $timelimit = (int) get_config('local_cleanupquestions', 'nightlycleanuptimelimit');
        $maxcourses = (int) get_config('local_cleanupquestions', 'nightlycleanupmaxcourses');

        // Default 1 hour per run if not configured, to avoid unbounded runs.
        if ($timelimit <= 0) {
            $timelimit = 60 * 60;
        }
        $stoptime = time() + $timelimit;

        // All course IDs (except site), sorted - we rotate through these across runs.
        // Use int so comparison with saved lastcourseid (int) works.
        $allcourseids = [];
        $courses = $DB->get_records('course', null, 'id ASC', 'id, fullname');
        foreach ($courses as $course) {
            if ($course->id == SITEID) {
                continue;
            }
            $allcourseids[] = (int) $course->id;
        }

        $totalcourses = count($allcourseids);
        if ($totalcourses === 0) {
            return;
        }

        // Resume from the course after the last one we processed (persisted across runs).
        $lastcourseid = (int) get_config('local_cleanupquestions', 'nightlycleanup_lastcourseid');
        $startindex = 0;
        if ($lastcourseid > 0) {
            $pos = array_search($lastcourseid, $allcourseids, true);
            if ($pos !== false) {
                $startindex = $pos + 1;
                if ($startindex >= $totalcourses) {
                    $startindex = 0; // New cycle: start from beginning.
                }
            }
        }

        // Build this run's list: from startindex, wrap around, optionally cap by maxcourses.
        $courseids = [];
        for ($i = 0; $i < $totalcourses; $i++) {
            $courseids[] = $allcourseids[($startindex + $i) % $totalcourses];
        }
        if ($maxcourses > 0) {
            $courseids = array_slice($courseids, 0, $maxcourses);
        }

        $summary = [
            'courses_processed' => 0,
            'courses_skipped' => 0,
            'duplicate_questions_deleted' => 0,
            'duplicate_questions_skipped' => 0,
            'empty_duplicate_categories_deleted' => 0,
            'empty_duplicate_categories_skipped' => 0,
            'empty_categories_deleted' => 0,
            'empty_categories_skipped' => 0,
            'unused_questions_deleted' => 0,
            'unused_questions_skipped' => 0,
            'time_limit_reached' => false,
            'started_at' => time(),
        ];

        $helper = new helper();
        $trace = new \text_progress_trace();

        $trace->output('Nightly cleanup started. Time limit: ' . $timelimit . 's, Max courses per run: ' . ($maxcourses > 0 ? $maxcourses : 'unlimited'));
        $trace->output('Total courses in site: ' . $totalcourses . ', resuming from course index ' . $startindex . ', processing ' . count($courseids) . ' course(s) this run');
        $trace->output('');

        $lastProcessedCourseId = null;
        foreach ($courseids as $courseid) {
            if (!$DB->record_exists('course', ['id' => $courseid])) {
                $summary['courses_skipped']++;
                $trace->output("Skipping non-existent course ID: $courseid");
                continue;
            }

            if ($stoptime && time() >= $stoptime) {
                $summary['time_limit_reached'] = true;
                break;
            }

            $course = $DB->get_record('course', ['id' => $courseid], 'id, fullname');
            $trace->output('');
            $trace->output('=======================================================');
            $trace->output("Course: {$course->fullname} (ID: {$courseid})");
            $trace->output('=======================================================');

            $helper->courseid = $courseid;

            $trace->output('Step 1/4: Deleting duplicate questions...');
            [$deleted, $skipped] = $helper->delete_duplicate_questions($stoptime, $trace);
            $trace->output("  Result: Deleted $deleted, Skipped $skipped");
            $summary['duplicate_questions_deleted'] += $deleted;
            $summary['duplicate_questions_skipped'] += $skipped;

            if ($stoptime && time() >= $stoptime) {
                $summary['time_limit_reached'] = true;
                break; // Incomplete course: do not count, next run will retry it.
            }

            $trace->output('Step 2/4: Deleting empty duplicate categories...');
            [$deleted, $skipped] = $helper->delete_empty_duplicate_categories($stoptime, $trace);
            $summary['empty_duplicate_categories_deleted'] += $deleted;
            $summary['empty_duplicate_categories_skipped'] += $skipped;
            $trace->output("  Result: Deleted $deleted, Skipped $skipped");

            if ($stoptime && time() >= $stoptime) {
                $summary['time_limit_reached'] = true;
                break;
            }

            $trace->output('Step 3/4: Deleting empty categories...');
            [$deleted, $skipped] = $helper->delete_empty_categories($trace);
            $summary['empty_categories_deleted'] += $deleted;
            $summary['empty_categories_skipped'] += $skipped;
            $trace->output("  Result: Deleted $deleted, Skipped $skipped");

            if ($stoptime && time() >= $stoptime) {
                $summary['time_limit_reached'] = true;
                break;
            }

            $trace->output('Step 4/4: Deleting unused questions...');
            [$deleted, $skipped] = $helper->delete_unused_questions($stoptime, $trace);
            $trace->output("  Result: Deleted $deleted, Skipped $skipped");
            $summary['unused_questions_deleted'] += $deleted;
            $summary['unused_questions_skipped'] += $skipped;

            $summary['courses_processed']++;
            $lastProcessedCourseId = $courseid;

            if ($stoptime && time() >= $stoptime) {
                $summary['time_limit_reached'] = true;
                $trace->output('');
                $trace->output('Time limit reached. Stopping.');
                break;
            }
        }

        if ($lastProcessedCourseId !== null) {
            set_config('nightlycleanup_lastcourseid', $lastProcessedCourseId, 'local_cleanupquestions');
            $trace->output('Saved resume point: next run will continue after course ID ' . $lastProcessedCourseId);
        }

        $trace->output('');
        $trace->output('Nightly cleanup finished. Sending report to admins.');
        $trace->finished();

        $summary['finished_at'] = time();
        $summary['duration_seconds'] = $summary['finished_at'] - $summary['started_at'];
        $summary['last_processed_course_id'] = $lastProcessedCourseId;

        $this->send_report_to_admins($summary);
    }

    /**
     * Send the nightly cleanup statistics report to all users with site:config.
     *
     * @param array $summary Statistics from the run
     */
    protected function send_report_to_admins(array $summary) {
        global $DB;

        $admins = get_users_by_capability(
            \context_system::instance(),
            'moodle/site:config',
            'u.id, u.email',
            '',
            '',
            '',
            [],
            '',
            false,
            true
        );

        if (empty($admins)) {
            return;
        }

        $reporttext = $this->build_report_text($summary);
        $subject = get_string('nightlycleanupreportsubject', 'local_cleanupquestions');

        foreach ($admins as $admin) {
            $user = $DB->get_record('user', ['id' => $admin->id]);
            if (!$user) {
                continue;
            }

            $message = new message();
            $message->component = 'local_cleanupquestions';
            $message->name = 'nightlycleanupreport';
            $message->userfrom = core_user::get_noreply_user();
            $message->userto = $user;
            $message->subject = $subject;
            $message->fullmessage = $reporttext;
            $message->fullmessageformat = FORMAT_PLAIN;
            $message->fullmessagehtml = '<pre>' . htmlspecialchars($reporttext) . '</pre>';
            $message->smallmessage = get_string('nightlycleanupreportsmall', 'local_cleanupquestions');
            $message->notification = 1;
            message_send($message);
        }
    }

    /**
     * Build plain-text statistics report.
     *
     * @param array $summary
     * @return string
     */
    protected function build_report_text(array $summary) {
        $lines = [];
        $lines[] = get_string('nightlycleanupreporttitle', 'local_cleanupquestions');
        $lines[] = str_repeat('=', 50);
        $lines[] = '';

        $lines[] = get_string('nightlycleanupreportcourses', 'local_cleanupquestions', $summary['courses_processed']);
        $lines[] = get_string('nightlycleanupreportcoursesskipped', 'local_cleanupquestions', $summary['courses_skipped']);
        if ($summary['time_limit_reached']) {
            $lines[] = get_string('nightlycleanupreporttimelimitreached', 'local_cleanupquestions');
        }
        $lines[] = get_string('nightlycleanupreportduration', 'local_cleanupquestions', $summary['duration_seconds']);
        if (!empty($summary['last_processed_course_id'])) {
            $lines[] = get_string('nightlycleanupreportresumepoint', 'local_cleanupquestions', $summary['last_processed_course_id']);
        }
        $lines[] = '';

        $lines[] = get_string('nightlycleanupreportduplicatequestions', 'local_cleanupquestions', [
            'deleted' => $summary['duplicate_questions_deleted'],
            'skipped' => $summary['duplicate_questions_skipped'],
        ]);
        $lines[] = get_string('nightlycleanupreportemptyduplicatecategories', 'local_cleanupquestions', [
            'deleted' => $summary['empty_duplicate_categories_deleted'],
            'skipped' => $summary['empty_duplicate_categories_skipped'],
        ]);
        $lines[] = get_string('nightlycleanupreportemptycategories', 'local_cleanupquestions', [
            'deleted' => $summary['empty_categories_deleted'],
            'skipped' => $summary['empty_categories_skipped'],
        ]);
        $lines[] = get_string('nightlycleanupreportunusedquestions', 'local_cleanupquestions', [
            'deleted' => $summary['unused_questions_deleted'],
            'skipped' => $summary['unused_questions_skipped'],
        ]);
        $lines[] = '';

        $totaldeleted = $summary['duplicate_questions_deleted']
            + $summary['empty_duplicate_categories_deleted']
            + $summary['empty_categories_deleted']
            + $summary['unused_questions_deleted'];
        $totalskipped = $summary['duplicate_questions_skipped']
            + $summary['empty_duplicate_categories_skipped']
            + $summary['empty_categories_skipped']
            + $summary['unused_questions_skipped'];

        $lines[] = get_string('nightlycleanupreporttotal', 'local_cleanupquestions', [
            'deleted' => $totaldeleted,
            'skipped' => $totalskipped,
        ]);
        $lines[] = str_repeat('=', 50);

        return implode("\n", $lines);
    }
}
