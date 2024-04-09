<?php
namespace local_guardians\output;

defined('MOODLE_INTERNAL') || die();

use renderable;

class local_guardians_node_content implements renderable {
    private $content;
 
    public function __construct($content) {
        $this->content = $content;
    }
 
    public function get_content() {
        return $this->content;
    }
}