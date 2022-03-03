<?php
/**
* Plugin Name: Announcements Plugin
* Description: Display one annoucement from list on the begin of every post.
* Version: 1.0
* Requires at least: 5.0
* Requires PHP: 7.2
* Author: Mateusz Niecikowski
* License: GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

function announcements_admin_actions_register_menu(){
    add_options_page("Announcements Plugin", "Announcements Plugin", 'manage_options', "announcements", "announcements_admin_page");

}

add_action('admin_menu', 'announcements_admin_actions_register_menu');

function format_announcement($content){
    return "<div class=\"announcement_wrapper\">".$content."</div>";
}

function announcements_admin_page(){
    global $_POST;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $temp = get_option('announcements');

        $data = $_POST['posted_announcement_data'];
        if(isset($data)){
            if(!in_array($data, $temp)){
                array_push($temp, $data);
            }    
        } 

        $key = $_POST['index_to_delete'];
        if(isset($key)){
            unset($temp[$key]);
        }
        update_option('announcements', $temp);
    }

    $announcements = get_option('announcements');

    ?>
        <div class="wrap">
            <h1>Announcements Plugin</h1>
            <h4>Announcements:</h4>
            <?php
                foreach($announcements as $key => $value){
                    echo format_announcement($value);
                    echo "<form method=\"post\"> <input type=\"number\" name=\"index_to_delete\" value=\"$key\" hidden/> <input type=\"submit\" value=\"Remove\"/></form>";
                }
            ?>
            <h4>Add new</h4>
            <form method="post">
                <textarea name="posted_announcement_data" rows="20" cols="100"></textarea>
                <p class="submit"><input type="submit" value="Submit"></p>
            </form>
        </div>
    <?php
}

function naph_register_styles(){
    //register style
    wp_register_style('announcements_styles', plugins_url('/css/announcements.css', __FILE__));
    //enable style (load in meta of html)
    wp_enqueue_style('announcements_styles');
    }

add_action('init', 'naph_register_styles');


function insert_announcement_before_content($content){
    $announcements = get_option('announcements');
    if(( is_single() || is_page() ) && in_the_loop() && is_main_query()) {
        $announcement = $announcements[array_rand($announcements)];
        return format_announcement($announcement).$content;
    }
    return $content;
}

add_filter("the_content", "insert_announcement_before_content");

?>