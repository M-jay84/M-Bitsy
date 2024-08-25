<?php

namespace App\Libs;

use App\Libs\Tmdb;
use App\Libs\OMDB;
use App\Libs\Cache;

/**
* Get TMDB/OMDB Data
*/
class MDBS
{
    // Get TMDB Id From Url
    public static function getId($url)
    {
        $parts = explode('/', $url);
        $id_tmdb = strtok($parts[4], '-');
        return $id_tmdb;
    }

    // Save Film Details In Database
    public static function createFilm($id_tmdb, $id, $url = null)
    {
        // Get TMDB Api
        $tmdb = new TMDB(); // , true
        $film = $tmdb->getMovie($id_tmdb);
        // Get & Save Image
        $image_url = $tmdb->getImageURL('w300') . "" . $film->getPoster();
        $image = $tmdb->saveImage($image_url, CONFIG['UPLOADDIR'] . "/images/tmdb/film", $id_tmdb);
		if (extension_loaded('gd')) {	
            copyImage(CONFIG['UPLOADDIR'] . "/images/tmdb/film/$id_tmdb.jpg", CONFIG['UPLOADDIR'] . "/images/tmdb/film/$id_tmdb.webp");	
            unlink(CONFIG['UPLOADDIR'] . "/images/tmdb/film/$id_tmdb.jpg");	
            unlink(CONFIG['CACHE'] . "/tmdb_cache/tmdb_film_cache/$id_tmdb.cache");	
            $image = "$id_tmdb.webp";	
        }
        // Get Data
        $trailer = $film->getTrailer();
        $title = $film->getTitle();
        $duration = $film->duration();
        $genre = $film->genre();
        $plot = $film->getPlot();
        $actors = $film->actor();
        $casting = $actors;
        $casting_role = $casting[0];
        $casting_role = explode('*', $casting_role);
        $casting_nom = $casting[1];
        $casting_nom = explode('+', $casting_nom);
        $casting_img = $casting[2];
        $casting_img = explode('&', $casting_img);
        for ($i = 0; $i <= 3; $i++) {
            $bdd .= "$casting_nom[$i]*$casting_role[$i]*$casting_img[$i]&";
        }
        // Insert Data
        DB::insert('tmdb', [
            'id_tmdb'=>$id_tmdb, 'title'=>$title, 'duration'=>$duration, 'producer'=>'not working',
            'genre'=>$genre, 'plot'=>$plot, 'actor'=>$bdd, 'trailer'=>$trailer, 'date'=>null,
            'image'=>$image, 'url'=>$url, 'type'=>'movie', 'torrentid'=>$id
        ]);
        // Return To Torrent
        Url(Url("torrent/$id"), true);
    }

    // Fetch Movie Data From Database or Cache
    public static function getFilm($id_tmdb)
    {
        $TTCache = new Cache();
        $expires = 1728000; // Cache time in seconds
        if (($_data = $TTCache->Get("tmdb_cache/tmdb_film_cache/$id_tmdb", $expires)) === false) {
            // Get Data From DB
            $film = DB::fetchRow('tmdb', '*', ['id_tmdb =', 'type ='], [$id_tmdb,'movie']);
            // Data
            $_data["title"] = $film["title"];
            $_data["duration"] = $film["duration"];
            $_data["producer"] = $film["producer"];
            $_data["genre"] = $film["genre"];
            $_data["plot"] = $film["plot"];
            $_data["actors"] = $film["actor"];
            $_data["trailer"] = $film["trailer"];
            $_data["date"] = $film["date"];
            $_data["poster"] = $film["image"];
            $_data["type"] = "film";
            // Set Data In Cache
            $TTCache->Set("tmdb_cache/tmdb_film_cache/$id_tmdb", $_data, $expires);
        }

        return $_data;
    }

    // Save Show Details In Database
    public static function createSerie($id_tmdb, $id, $url = null)
    {
        // Get TMDB Api
        $tmdb = new TMDB(); // , true
        $serie = $tmdb->getTVShow($id_tmdb);
        // Get & Save Image
        $image_url = $tmdb->getImageURL('w300') . "" . $serie->getPoster();
        $image = $tmdb->saveImage($image_url, CONFIG['UPLOADDIR'] . "/images/tmdb/serie/", $id_tmdb);
		if (extension_loaded('gd')) {	
            copyImage(CONFIG['UPLOADDIR'] . "image/tmdb/serie/$id_tmdb.jpg", CONFIG['UPLOADDIR'] . "/images/tmdb/serie/$id_tmdb.jpg");	
            //unlink(CONFIG['UPLOADDIR'] . "/tmdb_cache/tmdb_serie_cache/$id_tmdb.jpg");	
            unlink(CONFIG['CACHE'] . "/tmdb_cache/tmdb_serie_cache/$id_tmdb.cache");	
            $image = "$id_tmdb.webp";	
        }
        // Get Data
        $title = $serie->getName();
        $season = $serie->getNumSeasons();
        $episode = $serie->getNumEpisodes();
        $status = $serie->getInProduction();
        $date = $serie->date();
        $creator = $serie->creator();
        $genre = $serie->genre();
        $plot = $serie->getOverview();
        $actors = $serie->actor();
        $casting = $actors;
        $casting_role = $casting[0];
        $casting_role = explode('*', $casting_role);
        $casting_nom = $casting[1];
        $casting_nom = explode('+', $casting_nom);
        $casting_img = $casting[2];
        $casting_img = explode('&', $casting_img);
        for ($i = 0; $i <= 3; $i++) {
            $bdd .= "$casting_nom[$i]*$casting_role[$i]*$casting_img[$i]&";
        }
        // Insert Data
        DB::insert('tmdb', [
           'id_tmdb'=>$id_tmdb, 'title'=>$title, 'image'=>$image, 'season'=>$season, 'episodes'=>$episode, 'status'=>$status,
           'date'=>$date, 'creator'=>$creator, 'genre'=>$genre, 'plot'=>$plot, 'actor'=>str_replace("http:", "https:", $bdd,), 'url'=>$url, 'type'=>'show', 'torrentid'=>$id
        ]);
        // Return To Torrent
        Url(Url("torrent/$id"), true);
    }

    // Fetch Show Data From Database or Cache
    public static function getSerie($id_tmdb)
    {
        $TTCache = new Cache();
        $expires = 1728000; // Cache time in seconds
        if (($_data = $TTCache->Get("tmdb_cache/tmdb_serie_cache/$id_tmdb", $expires)) === false) {
            // Get Data From DB
            $serie = DB::fetchRow('tmdb', '*', ['id_tmdb =', 'type ='], [$id_tmdb,'show']);
            // Data
            $_data["poster"] = $serie["image"];
            $_data["title"] = $serie["title"];
            $_data["season"] = $serie["season"];
            $_data["episode"] = $serie["episodes"];
            $_data["status"] = $serie["status"];
            $_data["date"] = $serie["date"];
            $_data["creator"] = $serie["creator"];
            $_data["genre"] = $serie["genre"];
            $_data["plot"] = $serie["plot"];
            $_data["actor"] = $serie["actor"];
            $_data["type"] = "serie";
            // Set Data In Cache
            $TTCache->Set("tmdb_cache/tmdb_serie_cache/$id_tmdb", $_data, $expires);
        }

        return $_data;
    }

     // Save Film Details In Database
    public static function createImdb($id_tmdb, $id, $url = null)
    {
        // Get TMDB Api
        $movie = new OMDB($id_tmdb);
        $imdb_id = $id_tmdb;
        // Get & Save Image
        $image = $movie->poster;
        // Get Data
        $trailer = '$imdb->getTrailerAsUrl()';
        $title = $movie->title;
        $duration = $movie->runtime;
        $genre = $movie->genre;
        $plot = $movie->plot;
        $bdd = $movie->actors;
        $date = date('Y-m-d H:i:s',strtotime($movie->released));;
         // Insert Data
         DB::insert('tmdb', [
            'id_tmdb'=>$id_tmdb, 'title'=>$title, 'duration'=>$duration, 'producer'=>'not working',
            'genre'=>$genre, 'plot'=>$plot, 'actor'=>$bdd, 'trailer'=>$trailer, 'date'=>$date,
            'image'=>$image, 'url'=>$url, 'type'=>$imdb_id, 'torrentid'=>$id
        ]);
    }

    // Fetch Movie Data From Database or Cache
    public static function getImdb($id_tmdb, $id)
    {
        $TTCache = new Cache();
        $expires = 1728000; // Cache time in seconds
        if (($_data = $TTCache->Get("imdb_cache/$id", $expires)) === false) {
            // Get Data From DB
            $imdb = DB::fetchRow('tmdb', '*', ['torrentid ='], [$id]);
            //var_dump($imdb);
            // Data
            $_data["title"] = $imdb["title"];
            $_data["duration"] = $imdb["duration"];
            $_data["producer"] = $imdb["producer"];
            $_data["genre"] = $imdb["genre"];
            $_data["plot"] = $imdb["plot"];
            $_data["actors"] = $imdb["actor"];
            $_data["trailer"] = $imdb["trailer"];
            $_data["duration"] = $imdb["duration"];
            $_data["poster"] = $imdb["image"];
            $_data["type"] = "imdb";
            // Set Data In Cache
            $TTCache->Set("imdb_cache/$id", $_data, $expires);
        }

        return $_data;
    }

    // Get Image Or Poster
    public static function getImage($row)
    {
        if ($row["imdb"] != '') {
            $img =  $row["poster"];
        } elseif ($row["tmdb"] != '') {
            if (in_array($row["cat_parent"], CONFIG['SerieCats'])) {
                $img =  $row["poster"];
            } elseif (in_array($row["cat_parent"], CONFIG['MovieCats'])) {
                $img =  $row["poster"];
            }
        } elseif ($row["image1"] != '') {
            $img = data_uri(CONFIG['UPLOADDIR'] . "/images/" . $row["image1"], $row['image1']);
        } elseif ($row["image2"] != '') {
            $img = data_uri(CONFIG['UPLOADDIR'] . "/images/" . $row["image2"], $row['image2']);
        } else {
            $img = "" . CONFIG['URLROOT'] . "/assets/images/misc/default_avatar.webp";
        }
		
        return $img;
    }

    public static function createMDB($torrentid, $stmt)
    {
        $DATA = array();
        if (!empty($stmt["imdb"])) {

            $total = DB::fetchColumn('tmdb', 'count(id)', ['torrentid ='], [$torrentid]);
            $id_tmdb = MDBS::getId($stmt["imdb"]);
            if ($total == 0) {
                MDBS::createImdb($id_tmdb, $torrentid, $stmt["imdb"]);
            }
            $_data = MDBS::getImdb($id_tmdb, $stmt['id']);

            $DATA = [
                'TITLE' => "<p class='text-center'><legend><b>$_data[title]</b></legend></p>",
                'PLOT' => "$_data[plot]",
                'ACTORS' => "<b> Actors : </b>$_data[actors]",
                'RELEASE' => "<b> Release : </b>" . date("d-m-Y", utc_to_tz_time($_data["release"])) . "",
                'DURATION' => "<b> Duration : </b>$_data[duration]",
                'GENRE' => "<b> Genre : </b>$_data[genre]",
                'POSTER' => $_data['poster']
            ];

            DB::update('torrents', ['poster'], ['imdb='], [$_data['poster'], $stmt["imdb"]]);

        } elseif (!empty($stmt["tmdb"]) && in_array($stmt["cat_parent"], CONFIG['SerieCats'])) {

            $id_tmdb = MDBS::getId($stmt["tmdb"]);
            $total = DB::fetchColumn('tmdb', 'COUNT(id)', ['id_tmdb =', 'type ='], [$id_tmdb,'show']);
            if ($total == 0) {
                MDBS::createSerie($id_tmdb, $torrentid, $stmt["tmdb"]);
            }
            $_data = MDBS::getSerie($id_tmdb);

            $casting = explode('&', $_data["actor"]);
            $actorimage = '';
            for ($i = 0; $i <= 3; $i++) {
                list($pseudo, $role, $image) = explode("*", $casting[$i]);;
                $actorimage .= "<div class='col-3'>";
                $actorimage .= "<img class='avatar3' src='" . $image . "' /><br>";
                $actorimage .= "$pseudo/$role<br></div>";
            }
            $DATA = [
                'TITLE' => "<p class='text-center'><legend><b>$_data[title]</b></legend></p>",
                'PLOT' => "$_data[plot]",
                'SEASONS' => "<b> Seasons : </b>$_data[season]",
                'EPISODES' => "<b> Episodes : </b>$_data[episode]",
                'DATE' => "<b> Date : </b>$_data[date]",
                'CREATOR' => "<b> Creator : </b>$_data[creator]",
                'GENRE' => "<b> Genre : </b>$_data[genre]",
                'ACTORS' => "<div class='row'>$actorimage</div>",
                'POSTER' => CONFIG['UPLOADDIR'] . "/images/tmdb/$_data[type]/$_data[poster]"
            ];
            $poster = data_uri(CONFIG['UPLOADDIR'] . "/images/tmdb/serie/$_data[poster]", CONFIG['UPLOADDIR'] . "/images/tmdb/serie/$_data[poster]");
            DB::update('torrents', ['poster'], ['tmdb='], [$poster, $stmt["tmdb"]]);

        } elseif (!empty($stmt["tmdb"]) && in_array($stmt["cat_parent"], CONFIG['MovieCats'])) {
            
            $id_tmdb = MDBS::getId($stmt["tmdb"]);
            $total = DB::fetchColumn('tmdb', 'COUNT(id)', ['id_tmdb =', 'type ='], [$id_tmdb,'movie']);
            if ($total == 0) {
                MDBS::createFilm($id_tmdb, $torrentid, $stmt["tmdb"]);
            }
            $_data = MDBS::getFilm($id_tmdb);
            
            $casting = explode('&', $_data["actors"]);
            $actorimage = '';
            for ($i = 0; $i <= 3; $i++) {
                list($pseudo, $role, $image) = explode("*", $casting[$i]);;
                $actorimage .= "<div class='col-3'>";
                $actorimage .= "<img class='avatar3' src='" . $image . "' /><br>";
                $actorimage .= "$pseudo/$role<br></div>";
            }
            $DATA = [
                'TITLE' => "<b>$_data[title]</b>",
                'PLOT' => "$_data[plot]",
                'DURATION' => "<b> Duration : </b>$_data[duration]",
                'GENRE' => "<b> Genre : </b>$_data[genre]",
                'ACTORS' => "<div class='row'>$actorimage</div>",
                'video' => "<embed src='https://www.youtube.com/v/" . str_replace("watch?v=", "v/", htmlspecialchars($_data["trailer"])) . "' type=\"application/x-shockwave-flash\"  height=\"410\" width='100%'></embed>",
                'POSTER' => CONFIG['UPLOADDIR'] . "/images/tmdb/$_data[type]/$_data[poster]"
            ];
            $poster = data_uri(CONFIG['UPLOADDIR'] . "/images/tmdb/film/$_data[poster]", CONFIG['UPLOADDIR'] . "/images/tmdb/film/" . $_data['poster']);
            DB::update('torrents', ['poster'], ['tmdb='], [$poster, $stmt["tmdb"]]);

        } else {
            $DATA = [
                'TITLE' => "<p class='text-center'> <legend><b>$stmt[name]</b></legend></p>",
                'PLOT' => "<b>". LANG["DESCRIPTION"].":</b><br>".format_comment($stmt['descr']).""
            ];
        }

        return $DATA;
    }
}