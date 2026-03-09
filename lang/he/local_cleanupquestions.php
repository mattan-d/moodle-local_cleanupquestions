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
 * Hebrew language strings.
 *
 * @package    local_cleanupquestions
 * @copyright  CentricApp LTD (Dev Team) <dev@centricapp.co.il>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'ניקוי שאלות';
$string['cleanupquestions'] = 'ניקוי שאלות';
$string['cleanupallcourses'] = 'ניקוי כל הקורסים';
$string['cleanuptaskname'] = 'משימת ניקוי שאלות';
$string['cleanuptaskqueued'] = 'משימת הניקוי נוספה לתור ותתחיל בקרוב. תקבל הודעה כאשר היא תסתיים.';
$string['confirmcleanup'] = 'האם אתה בטוח שברצונך להפעיל פעולות ניקוי עבור הקורס: {$a}?';
$string['confirmcleanupall'] = 'האם אתה בטוח שברצונך להפעיל פעולות ניקוי עבור כל הקורסים?';
$string['cleanupwillremove'] = 'פעולה זו תסיר:';
$string['duplicatequestions'] = 'שאלות כפולות (שומר את הגרסה הישנה ביותר)';
$string['emptyduplicatecategories'] = 'קטגוריות כפולות ריקות';
$string['emptycategories'] = 'קטגוריות ריקות';
$string['unusedquestions'] = 'שאלות שלא בשימוש';
$string['taskrunning'] = 'משימת ניקוי רצה כעת עבור קורס זה.';
$string['taskqueued'] = 'משימת ניקוי בתור ותרוץ ב: {$a}';
$string['taskalreadyrunning'] = 'לא ניתן להוסיף משימה חדשה - משימת ניקוי כבר רצה או בתור עבור קורס זה.';
$string['taskstatusinfo'] = 'אנא המתן עד שהמשימה הנוכחית תסתיים לפני הפעלת משימה חדשה.';
$string['cannotqueuewhilerunning'] = 'לא ניתן להוסיף משימת ניקוי חדשה בזמן שמשימה כבר רצה או בתור עבור קורס זה.';

$string['cleanupnotification'] = 'תקבל הודעה עם סיכום הניקוי כאשר המשימה תסתיים.';
$string['cleanupcompletesubject'] = 'ניקוי שאלות הושלם';
$string['cleanupcompletefor'] = 'הניקוי הושלם עבור: {$a}';
$string['cleanupresults'] = 'תוצאות הניקוי:';
$string['duplicatequestionsresult'] = 'שאלות כפולות: נמחקו {$a->deleted}, דולגו {$a->skipped}';
$string['emptyduplicatecategoriesresult'] = 'קטגוריות כפולות ריקות: נמחקו {$a->deleted}, דולגו {$a->skipped}';
$string['emptycategoriesresult'] = 'קטגוריות ריקות: נמחקו {$a->deleted}, דולגו {$a->skipped}';
$string['unusedquestionsresult'] = 'שאלות לא בשימוש: נמחקו {$a->deleted}, דולגו {$a->skipped}';
$string['totalsummary'] = 'סה"כ: {$a->deleted} פריטים נמחקו, {$a->skipped} פריטים דולגו';
$string['cleanupcompletesmall'] = 'ניקוי שאלות הסתיים';
$string['messageprovider:cleanupcomplete'] = 'הודעת השלמת משימת ניקוי';

$string['currentstatistics'] = 'סטטיסטיקה נוכחית (לפני הניקוי)';
$string['statisticstotal'] = 'סה"כ שאלות: {$a}';
$string['statisticsunused'] = 'שאלות לא בשימוש: {$a}';
$string['statisticsduplicates'] = 'שאלות כפולות: {$a}';
$string['statisticsemptyduplicatecategories'] = 'קטגוריות כפולות ריקות: {$a}';
$string['statisticsemptycategories'] = 'קטגוריות ריקות: {$a}';

$string['duplicatequestiondeleted'] = 'נמחקה שאלה כפולה: {$a->name} (מזהה: {$a->id})';
$string['duplicatequestionskippedusedinquiz'] = 'דולגה שאלה כפולה (נמצאת בשימוש בניסיונות): {$a->name} (מזהה: {$a->id}){$a->info}';
$string['duplicatequestionusageinfo'] = ' - נמצאת בשימוש ב-{$a->component}: "{$a->activity}" (מזהה קורס: {$a->courseid}, מזהה מודול: {$a->cmid})';
$string['duplicatequestionskippeddeletionfailed'] = 'דולגה שאלה כפולה (מחיקה נכשלה - {$a->reason}): {$a->name} (מזהה: {$a->id})';
$string['duplicatequestionskippederror'] = 'דולגה שאלה כפולה (שגיאה: {$a->error}): {$a->name} (מזהה: {$a->id})';
$string['duplicatequestionsprogress'] = 'נמחקו {$a->deleted}, דולגו {$a->skipped} מתוך {$a->total}';

$string['nightlycleanuptaskname'] = 'ניקוי לילי של כל הקורסים (בנק שאלות)';
$string['nightlycleanuptimelimit'] = 'ניקוי לילי: זמן ריצה מקסימלי';
$string['nightlycleanuptimelimit_help'] = 'זמן ריצה מקסימלי (בשניות/דקות/שעות) לכל ריצה לילית. כשמגיעים למגבלה המשימה נעצרת ונשלח דוח. הגדר "ללא הגבלה" לעיבוד כל הקורסים בריצה אחת (לא מומלץ באתרים גדולים).';
$string['nightlycleanupmaxcourses'] = 'ניקוי לילי: מקסימום קורסים לריצה';
$string['nightlycleanupmaxcourses_help'] = 'מספר מקסימלי של קורסים לעיבוד בריצה לילית אחת. 0 = ללא הגבלה (רק מגבלת הזמן חלה). שימושי לפיזור הניקוי על פני כמה לילות באתרים גדולים.';
$string['nightlycleanupreporttitle'] = 'ניקוי שאלות לילי – דוח סטטיסטיקה';
$string['nightlycleanupreportcourses'] = 'קורסים שעובדו: {$a}';
$string['nightlycleanupreportcoursesskipped'] = 'קורסים שדולגו (לא קיימים): {$a}';
$string['nightlycleanupreporttimelimitreached'] = 'הריצה נעצרה: הגעת למגבלת זמן.';
$string['nightlycleanupreportduration'] = 'משך: {$a} שניות';
$string['nightlycleanupreportduplicatequestions'] = 'שאלות כפולות: {$a->deleted} נמחקו, {$a->skipped} דולגו';
$string['nightlycleanupreportemptyduplicatecategories'] = 'קטגוריות כפולות ריקות: {$a->deleted} נמחקו, {$a->skipped} דולגו';
$string['nightlycleanupreportemptycategories'] = 'קטגוריות ריקות: {$a->deleted} נמחקו, {$a->skipped} דולגו';
$string['nightlycleanupreportunusedquestions'] = 'שאלות לא בשימוש: {$a->deleted} נמחקו, {$a->skipped} דולגו';
$string['nightlycleanupreporttotal'] = 'סה"כ: {$a->deleted} פריטים נמחקו, {$a->skipped} פריטים דולגו';
$string['nightlycleanupreportsubject'] = 'ניקוי שאלות לילי – דוח סטטיסטיקה';
$string['nightlycleanupreportsmall'] = 'ניקוי השאלות הלילי הסתיים. ראה דוח מלא בהודעה.';
$string['messageprovider:nightlycleanupreport'] = 'דוח סטטיסטיקת ניקוי שאלות לילי';
