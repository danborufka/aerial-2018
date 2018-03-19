SELECT * FROM as_registrations AS A WHERE status = 3 AND A.course_id IN (SELECT course_id FROM `as_courses` WHERE )


DROP VIEW COURSES_LAST_DAY_DURING_PAST_DAY;
CREATE VIEW COURSES_LAST_DAY_DURING_PAST_DAY AS
SELECT name, course_id, IF(NOT ISNULL(date10), 10, IF(NOT ISNULL(date9), 9, IF(NOT ISNULL(date8), 8, IF(NOT ISNULL(date7), 7, IF(NOT ISNULL(date6), 6, IF(NOT ISNULL(date5), 5, IF(NOT ISNULL(date4), 4, IF(NOT ISNULL(date3), 3, 2)))))))) AS LAST_DATE, date1, date2, date3, date4, date5, date6, date7, date8, date9, date10 FROM as_courses WHERE NOT ISNULL(date2) AND (`end` BETWEEN (CURDATE() - INTERVAL 1 DAY) AND CURDATE())


CREATE VIEW COURSES_LAST_DAY_DURING_PAST_DAY_PRESENT AS
SELECT A.*, B.name, B.LAST_DATE, B.date1, B.date2, B.date3, B.date4, B.date5, B.date6, B.date7, B.date8, B.date9, B.date10 FROM as_registrations A, COURSES_LAST_DAY_DURING_PAST_DAY B WHERE A.course_id = B.course_id AND (case B.LAST_DATE
  when 2 then A.present2
  when 3 then A.present3
  when 4 then A.present4
  when 5 then A.present5
  when 6 then A.present6
  when 7 then A.present7
  when 8 then A.present8
  when 9 then A.present9
  when 10 then A.present10
  when 11 then A.present10
  when 12 then A.present10
END = 1)


SELECT C.*, student.* FROM COURSES_LAST_DAY_DURING_PAST_DAY_PRESENT AS C
INNER JOIN as_students AS student ON student.student_id = C.student_id