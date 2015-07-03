<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//Font awesome functions

/**
 * Change for alias
 */
if (!function_exists('getFontAwesomeIconFromAlias')) {
    function getFontAwesomeIconFromAlias($fa_icon)
    {
        //Set the record map icon
        switch($fa_icon){
            case 'fa-automobile':
                $fa_icon = 'fa-car';
                break;
            case 'fa-bank':
                $fa_icon = 'fa-university';
                break;
            case 'fa-bar-chart-o':
                $fa_icon = 'fa-bar-chart';
                break;
            case 'fa-cab':
                $fa_icon = 'fa-taxi';
                break;
            case 'fa-close':
                $fa_icon = 'fa-times';
                break;
            case 'fa-dashboard':
                $fa_icon = 'fa-tachometer';
                break;
            case 'fa-edit':
                $fa_icon = 'fa-pencil-square-o';
                break;
            case 'fa-file-movie-o':
                $fa_icon = 'fa-file-video-o';
                break;
            case 'fa-file-photo-o':
                $fa_icon = 'fa-file-image-o';
                break;
            case 'fa-file-picture-o':
                $fa_icon = 'fa-file-image-o';
                break;
            case 'fa-file-sound-o':
                $fa_icon = 'fa-file-audio-o';
                break;
            case 'fa-file-zip-o':
                $fa_icon = 'fa-file-archive-o';
                break;
            case 'fa-flash':
                $fa_icon = 'fa-bolt';
                break;
            case 'fa-gear':
                $fa_icon = 'fa-cog';
                break;
            case 'fa-gears':
                $fa_icon = 'fa-cogs';
                break;
            case 'fa-genderless':
                $fa_icon = 'fa-circle-thin';
                break;
            case 'fa-group':
                $fa_icon = 'fa-users';
                break;
            case 'fa-hotel':
                $fa_icon = 'fa-bed';
                break;
            case 'fa-image':
                $fa_icon = 'fa-picture-o';
                break;
            case 'fa-institution':
                $fa_icon = 'fa-university';
                break;
            case 'fa-legal':
                $fa_icon = 'fa-gavel';
                break;
            case 'fa-life-bouy':
                $fa_icon = 'fa-life-ring';
                break;
            case 'fa-life-buoy':
                $fa_icon = 'fa-life-ring';
                break;
            case 'fa-life-saver':
                $fa_icon = 'fa-life-ring';
                break;
            case 'fa-mail-forward':
                $fa_icon = 'fa-share';
                break;
            case 'fa-mail-reply':
                $fa_icon = 'fa-reply';
                break;
            case 'fa-mail-reply-all':
                $fa_icon = 'fa-reply-all';
                break;
            case 'fa-mobile-phone':
                $fa_icon = 'fa-mobile';
                break;
            case 'fa-mortar-board':
                $fa_icon = 'fa-graduation-cap';
                break;
            case 'fa-navicon':
                $fa_icon = 'fa-bars';
                break;
            case 'fa-photo':
                $fa_icon = 'fa-picture-o';
                break;
            case 'fa-remove':
                $fa_icon = 'fa-times';
                break;
            case 'fa-reorder':
                $fa_icon = 'fa-bars';
                break;
            case 'fa-paper-plane':
                $fa_icon = 'fa-send';
                break;
            case 'fa-paper-plane-o':
                $fa_icon = 'fa-send-o';
                break;
            case 'fa-futbol-o':
                $fa_icon = 'fa-soccer-ball-o';
                break;
            case 'fa-sort-down':
                $fa_icon = 'fa-sort-desc';
                break;
            case 'fa-sort-up':
                $fa_icon = 'fa-sort-asc';
                break;
            case 'fa-star-half-empty':
                $fa_icon = 'fa-star-half-o';
                break;
            case 'fa-star-half-full':
                $fa_icon = 'fa-star-half-o';
                break;
            case 'fa-support':
                $fa_icon = 'fa-life-ring';
                break;
            case 'fa-toggle-down':
                $fa_icon = 'fa-caret-square-o-down';
                break;
            case 'fa-toggle-right':
                $fa_icon = 'fa-caret-square-o-right';
                break;
            case 'fa-toggle-left':
                $fa_icon = 'fa-caret-square-o-left';
                break;
            case 'fa-toggle-up':
                $fa_icon = 'fa-caret-square-o-up';
                break;
            case 'fa-unsorted':
                $fa_icon = 'fa-sort';
                break;
            case 'fa-warning':
                $fa_icon = 'fa-exclamation-triangle';
                break;
        }

        return $fa_icon;
    }

}

?>