<?php

/*
 * libBokzuy - A Bokzuy PHP API library
 *
 * === RELEASE NOTES ===
 *    2012-02-08 - v0.1 - first version
 */

/* 
 * Copyright 2012  David Barragán Merino (david.barragan@kaleidos.net)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 * Online: http://www.gnu.org/licenses/gpl.txt
 */

define('BOKZUY_API_URL', 'https://api.bokzuy.com');


class Bokzuy{
    var $user_auth;
    var $user_id;
   
    function Bokzuy($email=null, $password=null){
        if (!empty($email) and !empty($password))
            $this->user_auth = $email.':'.$password;
    }
 
    function __GET_REQUEST($url, $options, $data){    
        $request = new HttpRequest($url, HttpRequest::METH_GET);
        $request->setOptions($options);
        $request->addQueryData($data);

        try{
            $request->send();
            if ($request->getResponseCode() == 200){
                return $request->getResponseBody();
            }
        }catch(HttpException $ex){
            return null;
        }
    }

    function __POST_REQUEST($url, $options, $fields){
        $request = new HttpRequest($url, HttpRequest::METH_POST);
        $request->setOptions($options);
        $request->addPostFields($fields);

        try{
            return  $request->send()->getBody();
        }catch (HttpException $ex){
            return null;
        }
    }

    function authenticate($email=null, $password=null){  
        if (!empty($email) and !empty($password))
            $user_auth = $email.':'.$password;

        $url = BOKZUY_API_URL.'/me/';
        $options = array('httpauth' => $this->user_auth);
        $data = array();
    
        $content = json_decode($this->__GET_REQUEST($url, $options, $data));

        if ($content && $content->success){
            $this->user_id = $content->user->id;
            return $content->user;
        }
        return null;
    }

    function get_last_received_badges_from_me($lang = 'en', $count = 6, $ignoreComments=false, $ignoreDeserves= false){
        $url = BOKZUY_API_URL.'/me/bokies';
        $options = array('httpauth' => $this->user_auth);
        $data = array('lang' => $lang, 
                      'max' => $count,
                      'ignoreComments' => $ignoreComments, 
                      'ignoreDeserves' => $ignoreDeserves);

        $content = json_decode($this->__GET_REQUEST($url, $options, $data));

        if ($content && $content->success){
            return $content->bokies;
        }
        return null;
    }

}

?>
