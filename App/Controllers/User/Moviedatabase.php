<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Libs\Tmdb;

// Get TMDB Classes
require_once CONFIG['APPROOT'] . "/Libs/TMDB.php";

/*
* TMDB Viewer Controller
*/
class Moviedatabase extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
    }

    // TMDB Search Default Page todo (tidy file)
    public function index()
    {
    

        // Check TMDB Set
        if (CONFIG['_TMDBswitch'] === false) {
            showMessage(Url(), LANG["TMDB_OFF"]);
        }
        $name = Input('search');
        $type = Input('type');
        $link = '';

        // Get TMDB Data
        $tmdb = new TMDB();

        if ($type == 'person') {
            $persons = $tmdb->searchPerson($name);
            foreach ($persons as $person) {
                $link .= $person->getName() . ' <a href="' . CONFIG['URLROOT'] . '/moviedatabase/details/' . $person->getID() . '/person"><b>Link</b></a><br>';
            }
        } elseif ($type == 'show') {
            $tvShows = $tmdb->searchTVShow($name);
            foreach ($tvShows as $tvShow) {
                $link .= $tvShow->getName() . ' <a href="' . CONFIG['URLROOT'] . '/moviedatabase/details/' . $tvShow->getID() . '/show"><b>Link</b></a><br>';
            }
        } elseif ($type == 'movie') {
            $movies = $tmdb->searchMovie($name);
            foreach ($movies as $movie) {
                $link .= $movie->getTitle() . ' <a href="' . CONFIG['URLROOT'] . '/moviedatabase/details/' . $movie->getID() . '/movie"><b>Link</b></a><br>';
            }
        }

        // Data For View
        $data = [
            'page_title' => LANG["Search"]. $type,
            'V_link' => $link,
            'V_type' => $type,
            'V_search' => $name
        ];

        // Load View
        $this->view('moviedatabase', $data, 'user');
    }

    // Details Page
    public function details($id,  $type,  $seasonnumber = 0) // todo
    {
    

        // Check TMDB Set
        if (CONFIG['_TMDBswitch'] === false) {
            showMessage(Url(), LANG["TMDB_OFF"]);
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
                $details .= $movieRole->getCharacter() . ' in <a href="' . CONFIG['URLROOT'] . '/moviedatabase/details/' . $movieRole->getMovieID() . '/movie">' . $movieRole->getMovieTitle() . '</a><br>';
            }
            $data['details'] = $details;

            $tvShowRoles = $_data->getTVShowRoles();
            $info = '<b>Show Roles</b><br>';
            foreach ($tvShowRoles as $tvShowRole) {
                $info .= $tvShowRole->getCharacter() . ' in <a href="' . CONFIG['URLROOT'] . '/moviedatabase/details/' . $tvShowRole->getTVShowID() . '/show">' . $tvShowRole->getTVShowName() . '</a><br>';
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
                $details .= "<a href='" . CONFIG['URLROOT'] . "/moviedatabase/details/" . $season->getID() . "/season/" . $season->getSeasonNumber() . "'>" . $season->getSeasonNumber() . "</a>&nbsp;";
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
                $info .= "<a href='" . CONFIG['URLROOT'] . "/moviedatabase/details/" . $casting_id[$i] . "/person'><img class='avatar3' src='" . $casting_img[$i] . "' /></a><br>" . $casting_nom[$i] . "<br>" . $casting_role[$i] . "&nbsp;";
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
                $info .= "<a href='" . CONFIG['URLROOT'] . "/moviedatabase/details/" . $casting_id[$i] . "/person'><img class='avatar3' src='" . $casting_img[$i] . "' /></a><br>" . $casting_nom[$i] . "<br>" . $casting_role[$i] . "&nbsp;";
                $info .= "</div>";
            }
            $info .= "</div>";
            $data['info'] = $info;

        } elseif ($type == 'season') {

            $_data = $tmdb->getSeason($id, $seasonnumber);

        }

        // Data For View
        $data = [
            'V_id' => $id,
            'page_title' => $data['title'],
            'V_poster' => $data['poster'],
            'V_details' => $data['details'],
            'V_info' => $data['info'],
            'V_seasonnumber' => $seasonnumber,
            'V_search' => Input('seach')
        ];

        // Load View
        $this->view('moviedatabase_details', $data, 'user');

    }
}