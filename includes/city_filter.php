<?php

function digichefsNormalizeCityFilter($city)
{
    $city = strtolower(trim((string) $city));
    $city = preg_replace('/[^a-z0-9]+/', ' ', $city);
    return trim(preg_replace('/\s+/', ' ', $city));
}

function digichefsIsMumbaiRegionSearch($city)
{
    $city = digichefsNormalizeCityFilter($city);

    return in_array($city, array(
        'mumbai',
        'mumbai city',
        'mumbai region',
        'greater mumbai',
        'bombay'
    ), true);
}

function digichefsMumbaiRegionTerms()
{
    return array(
        'Mumbai',
        'Bombay',
        'Navi Mumbai',
        'Thane',
        'Mira Road',
        'Bhayandar',
        'Vasai',
        'Virar',
        'Kalyan',
        'Dombivli',
        'Dombivali',
        'Panvel',
        'Ulwe',
        'Airoli',
        'Vashi',
        'Nerul',
        'Belapur',
        'Andheri',
        'Bandra',
        'Borivali',
        'Dahisar',
        'Goregaon',
        'Malad',
        'Kandivali',
        'Powai',
        'Ghatkopar',
        'Mulund',
        'Chembur',
        'Kurla'
    );
}

function digichefsBuildCityFilterSql($connect, $columnName, $city)
{
    $city = trim((string) $city);
    if ($city === '') {
        return '';
    }

    if (!digichefsIsMumbaiRegionSearch($city)) {
        return $columnName . " LIKE '%" . $connect->real_escape_string($city) . "%'";
    }

    $conditions = array();
    foreach (digichefsMumbaiRegionTerms() as $term) {
        $conditions[] = $columnName . " LIKE '%" . $connect->real_escape_string($term) . "%'";
    }

    return '(' . implode(' OR ', $conditions) . ')';
}
