<?php

if (! function_exists('adminFields')) {
    function adminFields() {
        $fieldList = ['Restaurant', 'Status','Icon'];

        return $fieldList;
    }
}

if (! function_exists('formatImage')) {
    function formatImage($image) {
        return TCG\Voyager\Facades\Voyager::image($image);
    }
}