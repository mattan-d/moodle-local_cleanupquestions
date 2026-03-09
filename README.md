# Cleanup Questions (local_cleanupquestions)

A Moodle local plugin for cleaning up question banks and quiz data: removing duplicate questions, empty categories, unused questions, and old quiz attempts.

**Copyright:** CentricApp LTD (Dev Team) <dev@centricapp.co.il>

## Requirements

- Moodle 4.0 or later
- Quiz module (`mod_quiz`)

## Installation

1. Copy the `cleanupquestions` folder into your Moodle `local/` directory.
2. Visit **Site administration → Notifications** and complete the upgrade.

## Features

- **Scheduled tasks:** Delete old quiz attempts (configurable lifetime); **nightly cleanup** of all courses with time/course limits and a statistics report sent to admins.
- **Question cleanup:** Remove duplicate questions (keeps oldest), empty duplicate categories, empty categories, and unused (hidden) questions.
- **Broken questions:** Fix or remove questions with missing options via CLI.
- **Web interface:** Run cleanup per course or for all courses from the course/site admin area.
- **CLI scripts:** All operations available from the command line for automation or large sites.

## Configuration

**Site administration → Plugins → Local plugins → Cleanup Questions**

- **Delete attempts older than** – Age (days/years) after which quiz attempts are deleted by the scheduled task. Set to "Do not delete old attempts" to disable scheduled deletion (CLI still available).
- **Delete unused hidden questions** – Whether the scheduled task should also delete unused hidden questions after removing old attempts.
- **Max execution time** – Time limit for the scheduled task (old attempts) to avoid overload.
- **Nightly cleanup: max execution time** – Time limit for the nightly “clean all courses” task (default: 1 hour). When reached, the task stops and sends a report.
- **Nightly cleanup: max courses per run** – Maximum number of courses to process per night (0 = no limit). Use to spread work over several nights on large sites.

The **nightly cleanup** task runs once per night (default 2:00 AM). It processes courses in order, runs the same four cleanup steps as the CLI “cleanup all”, respects the time and course limits above, then sends a **statistics report** to all users with `moodle/site:config` (popup + email). The report includes courses processed, items deleted/skipped per operation, total deleted/skipped, duration, and whether the time limit was reached.

## Web Interface

- **Per course:** In a course, use the "Cleanup Questions" link (course admin / settings area). Requires `moodle/site:config`.
- **All courses:** Use **Site administration → Plugins → Local plugins → Cleanup Questions** (the "Cleanup Questions" external page) to run cleanup for a single course or all courses.

Cleanup runs as an ad hoc task; you receive a notification when it finishes.

## CLI Scripts

Run from the Moodle root directory, e.g.:

```bash
php admin/cli/run.php --path=/path/to/moodle
# or run scripts directly (from Moodle root):
php local/cleanupquestions/cli/scriptname.php [options]
```

### Main cleanup (all operations)

```bash
php local/cleanupquestions/cli/cleanup_all.php [--courseid=ID] [--courseids=1,2,3] [--timelimit=300] [--verbose]
```

### Individual operations

- **Delete old quiz attempts**  
  `php local/cleanupquestions/cli/delete_attempts.php`  
  Options: `--days=90`, `--timestamp=...`, `--date="YYYY-MM-DD HH:MM:SS"`, `--timelimit=300`, `--verbose`

- **Delete unused (hidden) questions**  
  `php local/cleanupquestions/cli/delete_unused_questions.php`  
  Options: `--courseid=ID`, `--timelimit=300`, `--verbose`

- **Delete duplicate questions**  
  `php local/cleanupquestions/cli/delete_duplicate_questions.php`  
  Options: `--courseid=ID`, `--timelimit=300`, `--verbose`

- **Delete empty duplicate categories**  
  `php local/cleanupquestions/cli/delete_empty_duplicate_categories.php`  
  Options: `--courseid=ID`, `--timelimit=300`, `--verbose`

- **Delete empty categories**  
  `php local/cleanupquestions/cli/delete_empty_categories.php`  
  Options: `--courseid=ID`, `--timelimit=300`, `--verbose`

- **Fix missing question options**  
  `php local/cleanupquestions/cli/fix_missing_question_options.php`  
  Options: `--courseid=ID`, `--fix`, `--delete`, `--verbose`

- **Count questions (statistics)**  
  `php local/cleanupquestions/cli/count_questions.php`  
  Options: `--courseid=ID`, `--top=N`, `--verbose`

Use `--help` on any script for full option list.

## License

GNU GPL v3 or later. See the LICENSE file in the plugin directory.
