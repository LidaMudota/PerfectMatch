<?php

function getFullnameFromParts($surname, $name, $patronymic) {
    return $surname . ' ' . $name . ' ' . $patronymic;
}

function getPartsFromFullname($fullname) {
    $parts = explode(' ', $fullname);
    return [
        'surname' => $parts[0] ?? '',
        'name' => $parts[1] ?? '',
        'patronymic' => $parts[2] ?? ''
    ];
}

function validateFullname($fullname) {
    $parts = explode(' ', $fullname);
    if (count($parts) !== 3) {
        return false;
    }

    list($surname, $name, $patronymic) = $parts;

    // Проверка на то, что фамилия, имя и отчество не пустые
    if (empty($surname) || empty($name) || empty($patronymic)) {
        return false;
    }

    // Проверка на то, что фамилия и отчество имеют правильные окончания
    $surname_last_char = mb_substr($surname, -1);
    $patronymic_last_3_chars = mb_substr($patronymic, -3);
    $patronymic_last_2_chars = mb_substr($patronymic, -2);

    // Фамилия чаще всего заканчивается на согласную букву (можно дополнить по необходимости)
    $surname_valid_endings = ['в', 'н', 'й', 'ч', 'р', 'л', 'с', 'м', 'п', 'г', 'д', 'ж', 'з', 'т', 'к', 'б', 'ц', 'ф', 'х', 'ш', 'щ'];

    // Отчество заканчивается на "ич" или "вна"
    $patronymic_valid_endings = ['вна', 'ич'];

    if (!in_array($surname_last_char, $surname_valid_endings) || 
        (!in_array($patronymic_last_3_chars, $patronymic_valid_endings) && !in_array($patronymic_last_2_chars, $patronymic_valid_endings))) {
        return false;
    }

    return true;
}

function getShortName($fullname) {
    $parts = getPartsFromFullname($fullname);
    return $parts['name'] . ' ' . mb_substr($parts['surname'], 0, 1) . '.';
}

function getGenderFromName($fullname) {
    $parts = getPartsFromFullname($fullname);
    $gender = 0;

    // Определение пола по отчеству
    if (mb_substr($parts['patronymic'], -3) === 'вна') $gender--;
    if (mb_substr($parts['patronymic'], -2) === 'ич') $gender++;

    // Определение пола по имени
    if (in_array(mb_substr($parts['name'], -1), ['а', 'я'])) $gender--;
    if (in_array(mb_substr($parts['name'], -1), ['й', 'н'])) $gender++;

    // Определение пола по фамилии
    if (in_array(mb_substr($parts['surname'], -1), ['а', 'я'])) $gender--;
    if (in_array(mb_substr($parts['surname'], -1), ['в', 'н', 'й'])) $gender++;

    // Учет специфических окончаний имен
    if (mb_substr($parts['name'], -2) === 'ла') $gender--;  // Например, Никола
    if (mb_substr($parts['name'], -2) === 'ий') $gender++;  // Например, Юрий

    // Учет специфических фамилий
    if (mb_substr($parts['surname'], -2) === 'ва') $gender--;
    if (mb_substr($parts['surname'], -2) === 'ин') $gender++;
    if (mb_substr($parts['surname'], -2) === 'ая') $gender--;

    if ($gender > 0) {
        return 'Мужчина';
    } elseif ($gender < 0) {
        return 'Женщина';
    } else {
        return 'Неопределенный пол';
    }
}

function getGenderDescription($persons_array) {
    $gender_count = ['male' => 0, 'female' => 0, 'undefined' => 0];
    $total_age_male = 0;
    $total_age_female = 0;

    foreach ($persons_array as $person) {
        $gender = getGenderFromName($person['fullname']);
        if ($gender === 'Мужчина') {
            $gender_count['male']++;
            $total_age_male += $person['age'];
        } elseif ($gender === 'Женщина') {
            $gender_count['female']++;
            $total_age_female += $person['age'];
        } else {
            $gender_count['undefined']++;
        }
    }

    $total = count($persons_array);
    $male_percentage = round($gender_count['male'] / $total * 100, 1);
    $female_percentage = round($gender_count['female'] / $total * 100, 1);
    $undefined_percentage = round($gender_count['undefined'] / $total * 100, 1);

    $average_age_male = $total_age_male / $gender_count['male'];
    $average_age_female = $total_age_female / $gender_count['female'];

    return "Гендерный состав аудитории:\n---------------------------\nМужчины - $male_percentage% (средний возраст: $average_age_male лет)\nЖенщины - $female_percentage% (средний возраст: $average_age_female лет)\nНе удалось определить - $undefined_percentage%";
}

function getPerfectPartnerFromFullname($fullname, $persons_array) {
    $gender = getGenderFromName($fullname);

    if ($gender === 'Неопределенный пол') {
        return 'Невозможно определить идеальную пару для пользователя с неопределенным полом.';
    }

    do {
        $random_person = $persons_array[array_rand($persons_array)];
        $random_person_gender = getGenderFromName($random_person['fullname']);
    } while ($random_person_gender === $gender);

    $compatibility_percentage = rand(50, 100);
    return "Идеальная пара для $fullname: {$random_person['fullname']} ({$random_person['job']}) - совместимость $compatibility_percentage%";
}
?>