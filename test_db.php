<?php
header('Content-Type: application/json');
include 'c:/xampp/htdocs/hr/inc/config.php';
$res = $connect->query("SELECT l.id as lead_id, l.name, l.city, l.csalary, l.nperiod, ls.name as status, lsrc.name as source, rd.extracted_skills, l.skillset 
FROM tblleads l 
LEFT JOIN resume_documents rd ON l.id = rd.lead_id 
LEFT JOIN tblleadsstatus ls ON l.status = ls.id 
LEFT JOIN tblleadssources lsrc ON l.source = lsrc.id 
WHERE ls.name = 'New Candidate' AND lsrc.name = 'Google' AND l.nperiod = '0'");
$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = $r;
}
file_put_contents('c:/xampp/htdocs/hr/test_empty.json', json_encode(array_slice($rows, 0, 100), JSON_PRETTY_PRINT));
