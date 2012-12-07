<?php
/**
 *
 * @package    filter
 * @subpackage filtermenu
 * @author     comete
 *
 * Change the first instance of [-MENU-] to create the menu
 *
 * remember to change the text cache lifetime !
 * /admin/settings.php?section=commonfiltersettings
 * get_user_courses_bycap($userid, $cap, $accessdata_ignored, $doanything_ignored, $sort = 'c.sortorder ASC', $fields = null, $limit_ignored = 0)
 * $cap = moodle/course:view
 *
 * Quick way to try and debug the whole thing, in my/index.php:
 * =  echo $OUTPUT->blocks_for_region('content');
 * +  if ($USER->username === 'yourlogin') {
 * +    $text = 'menu: [-MENU-]';
 * +    include "../filter/filtermenu/filter.php";
 * +    $cn = new filter_filtermenu($text, array());
 * +    echo $cn->filter($text);
 * +  }
 *
 */

defined('MOODLE_INTERNAL') || die;

class filter_filtermenu extends moodle_text_filter {

    /**
     * @var array global configuration for this filter
     *
     * This might be eventually moved into parent class if we found it
     * useful for other filters, too.
     */
    public $globalconfig;
    public $debug;
    public $average_item_width = 32;
    public $menu_min_height = 4;
    public $average_item_height = 28;
    public $menu_lines_max = 25;



    /**
     * Apply the filter to the text
     *
     * @see filter_manager::apply_filter_chain()
     * @param string $text to be processed by the text
     * @param array $options filter options
     * @return string text after processing
     */
    public function filter($text, array $options = array()) {
        global $CFG, $USER;

        // user not connected: don't show anything
        if (!$USER->id) {
            $text = str_replace('[-MENU-]', '', $text);
        }
        // avoid unnecessary work
        if (strpos($text, '[-MENU-]') === false) {
            return $text;
        }

        // step 1 : look for the context for the start page : (id course = 1)
        $context = get_context_instance(CONTEXT_COURSE, 1);

        // Check the user's 'course_update_capability' in the context :
        if ((has_capability('moodle/course:update', $context)) && (isset($USER->id)) && ((isset($USER->username) && $USER->username <> 'guest'))) {
            $is_admin_filter = true;
        } else {
            $is_admin_filter = false;
        }
        // step 2 : select all for admin, own for others
        if ($is_admin_filter) {
            $USER->filtermenu = optional_param('filtermenu', 'all', PARAM_TEXT);
        } else {
            $USER->filtermenu = optional_param('filtermenu', 'own', PARAM_TEXT);
        }

        // If guest, show only courses with guest authorized
        if (isset($USER->username) && ($USER->username == 'guest')) {
            $USER->filtermenu = 'guest';
        }

        /// Build the tree of categories
        $categories = get_categories(0);
        $cattree = $this->load_cattree($categories);

        $filter = false;
        // we want the user's courses only
        if ($USER->filtermenu == 'own') {
            $courses = enrol_get_users_courses($USER->id, "fullname ASC, shortname ASC");
            foreach ($courses as $course) {
                if (!$course->category) {
                    continue;
                }
                $this->has_courses($course);
            }
        // we want the courses with key only
        } elseif ($USER->filtermenu == 'all') {
            $courses = get_courses('all', 'c.fullname ASC');
        // we want the courses open for guest ?
        } elseif ($USER->username == 'guest') {
            $courses = get_courses('all', 'c.fullname ASC');
            $filter = 'guest';
        }

        foreach ($courses as $course) {
            if (!$course->category) {
                continue;
            }
            // we call the function with the parameter $filter
            $this->has_courses($course, $filter);
        }

        $menu = $this->menu();

        $text = str_replace('[-MENU-]', $menu, $text);
        if (!empty($this->debug)) {
            $text = $this->debug."\n".$text;
        }
        return $text;
    }



    /**
     * Load all categories and sub categories
     *
     * @uses $categories;
     * @uses $USER;
     * @param int $cats Category id to analyse
     * @return array Category array completed
     */
    private function load_cattree($cats) {
        global $categories, $USER;
        foreach ($cats as $cat) {
            $categories[$cat->id]->id = $cat->id;
            $categories[$cat->id]->parent = $cat->parent;
            if ($cs = get_categories($cat->id)) {
                $categories[$cat->id]->categories = $this->load_cattree($cs);
            } else {
                $categories[$cat->id]->categories = array();
            }
            $categories[$cat->id]->hascourses = false;

            // If user can create courses, need to get all categories with hascourses = true to let them printed
            if ((has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM))) AND ($USER->filtermenu == 'all')) {
                $categories[$cat->id]->hascourses = true;
            }
        }
        return $categories;
    }



    /**
     * Check all courses and switch all parent categories with "hascourse" setting
     *
     * @uses $categories;
     * @param array $course Course array to analyse
     * @return nothing
     */
    private function has_courses($course, $filter=false) {
        global $categories;
        // if filter=true : add the courses with password only
        if (($filter=="guest" && $course->guest) || !$filter) {
            $categories[$course->category]->hascourses = true;
            $categories[$course->category]->courses[$course->sortorder] = $course;
            $catid = $course->category;
            while ($categories[$catid]->parent > 0) {
                $catid = $categories[$catid]->parent;
                $categories[$catid]->hascourses = true;
            }
        }
    }



	/**
	* Count how many lines we be displayed in the menu at maxi
	*
	* @uses $categories;
	* @uses $nbcat;
	* @uses $nbcourse;
	* @param $parent
	*/
	private function get_maxi($parent) {
		global $categories,$nbcat,$nbcourse;

		if ($result_cat = get_categories($parent)) {
			foreach ($result_cat as $cat) {
				if ($categories[$cat->id]->hascourses == true) {

					// Count categories
					$nbcat[$cat->parent] = @$nbcat[$cat->parent]+1;

					// Search in subcategories
					$this->get_maxi($cat->id);

					// Count courses
					if (@$categories[$cat->id]->courses) {
						foreach ($categories[$cat->id]->courses as $course) {
							if (strlen($course->fullname) > $this->average_item_width)
								$line=ceil(strlen($course->fullname)/$this->average_item_width);
							else
								$line = 1;
							if (isset($nbcourse[$cat->id])) {
								// if the display size limit isn't reached
								if ($nbcourse[$cat->id] < $this->menu_lines_max) {
									$nbcourse[$cat->id]=@$nbcourse[$cat->id]+$line;
								}
							}
							else {
								$nbcourse[$cat->id]=$line;
							}
						}
					}
				}
			}
		}
		return true;
	}



    /**
     * Load all categories and sub categories
     *
     * @uses $categories;
     * @uses $USER;
     * @param int $cats Category id to analyse
     * @return array Category array completed
     */
    private function menu() {
        global $CFG, $USER, $categories, $nbcat, $nbcourse;
		$maxi=0;
		$this->get_maxi(0);
		foreach ($categories as $cat) {
			if (((@$nbcat[$cat->id]) OR (@$nbcourse[$cat->id])) AND ((@$nbcat[$cat->id]+@$nbcourse[$cat->id]) > $maxi))
				$maxi = (@$nbcat[$cat->id]+@$nbcourse[$cat->id]);
		}
		if ($maxi < $this->menu_min_height)
			$maxi = $this->menu_min_height;
		$this->menu_lines_max = $maxi;
        $list = $this->menu_list(0);
		
		if (isset($USER->filtermenu)) {
			$title = 'title_'.$USER->filtermenu;
		} else {
			$title = 'title_guest';
		}
		
        if (!empty($list)) {
			$menu = '';
			// Import css file
			$menu .= '<link type="text/css" href="'.$CFG->wwwroot.'/filter/filtermenu/css/filter_filtermenu.css" media="screen" rel="stylesheet" />'."\n";
			if (file_exists($CFG->dirroot.'/theme/'.current_theme().'/style/filter_filtermenu.css')) {
				$menu .= '<link type="text/css" href="'.$CFG->wwwroot.'/theme/'.current_theme().'/style/filter_filtermenu.css" media="screen" rel="stylesheet" />'."\n";
			}
			
			// Print the menu
			$menu .= '<script type="text/javascript" src="'.$CFG->wwwroot.'/filter/filtermenu/js/browserdetect.min.js"></script>'."\n";
			$menu .= '<script type="text/javascript" src="'.$CFG->wwwroot.'/filter/filtermenu/js/dynMenu.min.js"></script>'."\n";
			$menu .= '<style type="text/css">#ancre_menu, #menu, #menu ul { height: '.($this->average_item_height*$this->menu_lines_max).'px !important; }</style>';


			// Print switch [all courses / my courses only]
			if ($USER->username <> 'guest') {
				$menu .= '<p class="switch">';
				if ($title == 'title_all') {
					$menu .= '<a href="'.$CFG->wwwroot.'/?filtermenu=own">'.get_string('viewmycourses', 'filter_filtermenu').'</a>';
				} else {
					$menu .= '<a href="'.$CFG->wwwroot.'/?filtermenu=all">'.get_string('viewallcourses', 'filter_filtermenu').'</a>';
				}
				$menu .= '</p>'."\n";
			}

			$menu .= '<h2>'.get_string($title, 'filter_filtermenu').'</h2>'."\n";

			$menu .=
				'<div id="filtermenu_container" align="center"><p id="ancre_menu"><ul id="menu">'."\n".
				$list.
				'</ul></p></div>'."\n".
				'<script type="text/javascript">initMenu();</script>'."\n".
			'';
		}
		// If $list is empty, we show a string and  the course_search
		else {
			$menu = '<h2>'.get_string($title, 'filter_filtermenu').'</h2>'."\n";		
			$menu .= '<p>'.get_string('nocourse', 'filter_filtermenu').'</p>';		
		}
		
		$menu .= print_course_search('' ,true);

        return $menu;
    }



    /**
     * Create the list of categories and course
     *
     * @uses $CFG;
     * @uses $USER;
     * @uses $categories;
     * @param int $parent Parent id to begin
     * @return bool
     */
    private function menu_list($parent) {
        global $CFG, $USER, $categories;
        $list='';

        if ($result_cat = get_categories($parent)) {

            foreach ($result_cat as $cat) {

                if ($categories[$cat->id]->hascourses == true) {
                    if ($cat->visible == 0){
                        $category_status = 'hidden';
                    } else {
                        $category_status = 'visible';
                    }
                    $list .= "\n".'<li class="category'.$category_status.'"><span class="gauche"><a title="Afficher tous les cours de la catégorie '.$cat->name.'" href="'.$CFG->wwwroot.'/course/category.php?id='.$cat->id.'&resort=name&sesskey='.$USER->sesskey.'" ><img src="'.$CFG->wwwroot.'/filter/filtermenu/pix/folder.png" /></a></span><a href="javascript:;" class="filtermenu_action" title="'.$cat->name.'"><span class="droite"><img src="'.$CFG->wwwroot.'/filter/filtermenu/pix/arrow.png" /></span>'.shorten_text($cat->name,100,true).'</a><ul>'."\n";

                    // Print sub categories
                    if ($this->menu_list($cat->id)) {
                        $list .= $this->menu_list($cat->id);
                    }

                    // Print courses
                    if (@$categories[$cat->id]->courses) {

						$course_counter = 0;
						$displaylink = false;
                        // counter size limit
                        foreach ($categories[$cat->id]->courses as $course) {

							if (strlen($course->fullname) > $this->average_item_width)
								$course_counter+= ceil(strlen($course->fullname) / $this->average_item_width);
							else
								$course_counter++;
							// the "<=" is necessary ! do not use < only !
							if ($course_counter <= $this->menu_lines_max) {
								// Get user status in this course
								$context = get_context_instance(CONTEXT_COURSE, $course->id);
                                if ((has_capability('moodle/course:update', $context)) AND ($USER->id) AND ($USER->username <> 'guest')) {
                                    $user_status = 'editingteacher';
                                } elseif ((has_capability('gradereport/grader:view', $context)) AND ($USER->id) AND ($USER->username <> 'guest')) {
									$user_status = 'teacher';
								} elseif ((has_capability('moodle/course:view', $context)) AND ($USER->id) AND ($USER->username <> 'guest')) {
									$user_status = 'student';
								} else {
									// If no user status, get access conditions
									if ($course->password) {
										$user_status = 'key';
									// If guest and guest didn't need key in this course
									} elseif ($course->guest == 1)
										$user_status = 'guest';
									else
										$user_status = '';
								}

								// Check if this course is visible
								if ($course->visible == 0) {
									$course_status='hidden';
								} else {
									$course_status='visible';
								}

								$list .= "\n".'<li class="course'.$course_status.'">';

								if ($user_status) {
									$list .= '<span class="droite_status"><img title="'.get_string($user_status, 'filter_filtermenu').'" height="16" width="16" border="0" src="'.$CFG->wwwroot.'/filter/filtermenu/pix/'.$user_status.'.png" /></span>';
								}

								$list .= '<a title="'.$course->shortname." - ".$course->fullname.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'" >'.shorten_text($course->fullname,100,true).'</a>'."\n";
							}
							// stop when the limit is reached
							else {
								$displaylink = true;
								break;
							}
						}
						// if the limit was reached, display the link to the category
						if ($displaylink)
							$list .= '<li class="toomanycourses"><a title="'.get_string('toomanycourses', 'filter_filtermenu').'" href="'.$CFG->wwwroot.'/course/category.php?id='.$cat->id.'&resort=name&sesskey='.$USER->sesskey.'" >'.get_string('seeallcourses', 'filter_filtermenu').'</a>';

                    }
                    $list .= '</ul></li>'."\n";
                }
            }
            return $list;
        }
        return false;
    }




    function debug($var, $title='')
    {
        if (empty($this->debug)) {
            $this->debug .= '<style>'."\n";
            $this->debug .= 'div.debug-title{margin:20px 0 -20px 0;padding:4px;background:#666;border:1px #999 solid;color:#fff;font-weight:bold;}'."\n";
            $this->debug .= 'div.debug{margin:20px 0;padding:4px;background:#ddd;border:1px #999 solid;color:#666;}'."\n";
            $this->debug .= '</style>'."\n";
        }
        $var = print_r($var, true);
        $var = str_replace(str_repeat(' ',4), "\t", $var);
        $var = str_replace("\t", str_repeat('&nbsp;',4), $var);
        $var = nl2br($var);
        if (!empty($title)) $this->debug .= '<div class="debug-title">'.$title.'</div>'."\n";
        $this->debug .= '<div class="debug">'.$var.'</div>'."\n";
    }

}
