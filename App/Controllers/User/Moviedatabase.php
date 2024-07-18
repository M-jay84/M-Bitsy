<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Libs\Tmdb;

// Get TMDB Classes
require_once APPROOT . "/Libs/TMDB.php";

/*
* TMDB Viewer Controller
*/
class Moviedatabase 
{
    // TMDB Search Default Page todo (tidy file)
    public function index()
    {
        $USER = new User(0, 2);

        // Check TMDB Set
        if (_TMDBswitch === false) {
            Redirect(Url(), Txt("TMDB_OFF"));
        }
        $name = Input('search');
        $type = Input('type');
        $link = '';

        // Get TMDB Data
        $tmdb = new TMDB();

        if ($type == 'person') {
            $persons = $tmdb->searchPerson($name);
            foreach ($persons as $person) {
                $link .= $person->getName() . ' <a href="' . URLROOT . '/moviedatabase/details/' . $person->getID() . '/person"><b>Link</b></a><br>';
            }
        } elseif ($type == 'show') {
            $tvShows = $tmdb->searchTVShow($name);
            foreach ($tvShows as $tvShow) {
                $link .= $tvShow->getName() . ' <a href="' . URLROOT . '/moviedatabase/details/' . $tvShow->getID() . '/show"><b>Link</b></a><br>';
            }
        } elseif ($type == 'movie') {
            $movies = $tmdb->searchMovie($name);
            foreach ($movies as $movie) {
                $link .= $movie->getTitle() . ' <a href="' . URLROOT . '/moviedatabase/details/' . $movie->getID() . '/movie"><b>Link</b></a><br>';
            }
        }

        // Init Data
        $data = [
            'V_title' => Txt("Search"). $type,
            'V_link' => $link,
            'V_type' => $type,
            'V_search' => $name,
            'USER' => $USER
        ];

        // Load View
        template('moviedatabase', $data, 'user');
    }

    // Details Page
    public function details($id,  $type,  $seasonnumber = 0) // todo
    {
        $USER = new User(0, 2);

        // Check TMDB Set
        if (_TMDBswitch === false) {
            Redirect(Url(), Txt("TMDB_OFF"));
        }
        // Get TMDB Data
        $tmdb = new TMDB();

        if ($type == 'person') {
            $_data = $tmdb->getPerson($id);

            $data['title'] = $_data->getName();

            $poster = '<img src="' . $tmdb->getImageURL('w185') . $_data->getProfile() . '"/><br>';
            $poster .= 'Birthday<br>' . $_data->getBirthday() . '</br><br>';
            $data['poster'] = $poster;

            $movieRoles = $_data->getMovieRoles();
            $details = '<b>Movie Roles</b><br>';
            foreach ($movieRoles as $movieRole) {
                $details .= $movieRole->getCharacter() . ' in <a href="' . URLROOT . '/moviedatabase/details/' . $movieRole->getMovieID() . '/movie">' . $movieRole->getMovieTitle() . '</a><br>';
            }
            $data['details'] = $details;

            $tvShowRoles = $_data->getTVShowRoles();
            $info = '<b>Show Roles</b><br>';
            foreach ($tvShowRoles as $tvShowRole) {
                $info .= $tvShowRole->getCharacter() . ' in <a href="' . URLROOT . '/moviedatabase/details/' . $tvShowRole->getTVShowID() . '/show">' . $tvShowRole->getTVShowName() . '</a><br>';
            }
            $data['info'] = $info;

        } elseif ($type == 'show') {

            $_data = $tmdb->getTVShow($id);

            $data['title'] = $_data->getName();

            $data['poster'] = '<img src="' . $tmdb->getImageURL('w185') . $_data->getPoster() . '"/>';

            $details = '';
            $details .= '<b>' . $_data->genre() . '</b><br><br>';
            $details .= '<b>' . $_data->getOverview() . '</b><br><br>';
            $details .= 'Creator ' . $_data->creator() . '<br><br>';
            $details .= '<b>' . $_data->getNumEpisodes() . '</b> Episodes<br><br>';
            $details .= '' . $_data->getNumSeasons() . '  Seasons<br>';
            $seasons = $_data->getSeasons();
            foreach ($seasons as $season) {
                $details .= "<a href='" . URLROOT . "/moviedatabase/details/" . $season->getID() . "/season/" . $season->getSeasonNumber() . "'>" . $season->getSeasonNumber() . "</a>&nbsp;";
            }
            $data['details'] = $details;

            $actors = $_data->actor();
            $casting = $actors;
            $casting_role = $casting[0];
            $casting_role = explode('*', $casting_role);
            $casting_nom = $casting[1];
            $casting_nom = explode('+', $casting_nom);
            $casting_img = $casting[2];
            $casting_img = explode('&', $casting_img);
            $casting_id = $casting[3];
            $casting_id = explode('&', $casting_id);
            $info = "<div class='row'>";
            for ($i = 0; $i <= 3; $i++) {
                $info .= "<div class='col-3'>";
                $info .= "<a href='" . URLROOT . "/moviedatabase/details/" . $casting_id[$i] . "/person'><img class='avatar3' src='" . $casting_img[$i] . "' /></a><br>" . $casting_nom[$i] . "<br>" . $casting_role[$i] . "&nbsp;";
                $info .= "</div>";
            }
            $info .= "</div>";
            $data['info'] = $info;

        } elseif ($type == 'movie') {
            /*
            //<!-- movie -->
            <div class="col-12 col-md-4"> <?php
            print("<embed src='https://www.youtube.com/v/" . str_replace("watch?v=", "v/", htmlspecialchars($movie->getTrailer())) . "' type=\"application/x-shockwave-flash\"  height=\"410\" width='100%'></embed>"); ?>
            </div>
            */
            $_data = $tmdb->getMovie($id);

            $data['title'] = $_data->getTitle();

            $data['poster'] = '<img src="' . $tmdb->getImageURL('w185') . $_data->getPoster() . '"/>';

            $details = '';
            $details .= '<b>' . $_data->genre() . '</b><br><br>';
            $details .= '<b>' . $_data->getTagline() . '</b><br><br>';
            $details .= '<b>' . $_data->getplot() . '</b><br><br>';
            $details .= '<b>Duration ' . $_data->duration() . '</b><br>';
            $data['details'] = $details;

            $actors = $_data->actor();
            $casting = $actors;
            $casting_role = $casting[0];
            $casting_role = explode('*', $casting_role);
            $casting_nom = $casting[1];
            $casting_nom = explode('+', $casting_nom);
            $casting_img = $casting[2];
            $casting_img = explode('&', $casting_img);
            $casting_id = $casting[3];
            $casting_id = explode('&', $casting_id);
            $info = "<div class='row'>";
            for ($i = 0; $i <= 3; $i++) {
                $info .= "<div class='col-3'>";
                $info .= "<a href='" . URLROOT . "/moviedatabase/details/" . $casting_id[$i] . "/person'><img class='avatar3' src='" . $casting_img[$i] . "' /></a><br>" . $casting_nom[$i] . "<br>" . $casting_role[$i] . "&nbsp;";
                $info .= "</div>";
            }
            $info .= "</div>";
            $data['info'] = $info;

        } elseif ($type == 'season') {

            $_data = $tmdb->getSeason($id, $seasonnumber);

        }

        // Init Data
        $data = [
            'V_id' => $id,
            'V_title' => $data['title'],
            'V_poster' => $data['poster'],
            'V_details' => $data['details'],
            'V_info' => $data['info'],
            'V_seasonnumber' => $seasonnumber,
            'V_search' => Input('seach'),
            'USER' => $USER
        ];

        // Load View
        template('moviedatabase_details', $data, 'user');

    }
}