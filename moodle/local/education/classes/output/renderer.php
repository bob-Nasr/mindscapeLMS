<?php
namespace local_education\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;

class renderer extends plugin_renderer_base {
    public function render_local_education_node_content(local_education_node_content $content) {
        return $this->render_from_template('local_education/node_content', ['content' => $content->get_content()]);
    }
}