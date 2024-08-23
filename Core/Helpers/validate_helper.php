<?php

// Multi $_POST ids
function multiIds($value)
{
    $ids = $_POST[$value];
    if (!$ids) {
        showMessage(Url(), LANG["MISSING_FORM_DATA"]);
    }
    $int_array = array_map('intval', $ids);
    $string_of_ids = implode(', ', $int_array);

    return $string_of_ids;
}

// Privacy Level
function priv($name, $descr)
{
    if ($_SESSION['privacy'] == $name) {
        return "<input type=\"radio\" name=\"privacy\" value=\"$name\" checked=\"checked\" /> $descr";
    }
    return "<input type=\"radio\" name=\"privacy\" value=\"$name\" /> $descr";
}