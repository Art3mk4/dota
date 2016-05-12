<?php
/**
 * Author: Vitaly Kukin
 * Date: 11.11.2015
 * Time: 13:16
 */

namespace appTemplate;

class Template {

    /**
     * The title of admin interface
     * @access public
     * @var string
     */
    public $title = "";

    /**
     * The icon of title
     * @access public
     * @var string
     */
    public $icon = "";

    /**
     * Description of admin page
     * @access public
     * @var string
     */
    public $description = "";

    /**
     * The current page of plugin
     * @access public
     * @var string
     */
    public $current = "";

    /**
     * Message
     * @access public
     * @var string
     */
    public $message = "";

    public $message_type = "info";

    public $tmpls = array();

    public $menu = array();

    public $page = '';

    public $page_var = '';

	public $page_code = 'xpage';

    public function __construct() {
        $this->currentMenu();
    }

    /**
     * list template slugs
     * @return array
     */
    public function templList( $args = array() ) {

        return $this->tmpls = $args;
    }

    /**
     *	list menu array
     */
    public function listMenu( $args = array() ) {

        $this->menu = $args;

        /*
        return array(
            'sample_slug' 	=> array(
                'title' 		=> 'Sample Title',
                'description'	=> 'Sample Description',
                'icon' 			=> 'icon code',
                'submenu' 		=> array(
                    'sample_slug' 		=> array(
                        'title' 		=> 'Sample Title Submenu',
                        'description' 	=> 'Sample Description'
                    ),
                    'sample_slug2' 		=> array(
                        'title' 		=> 'Sample Title Submenu2',
                        'description' 	=> 'Sample Description2'
                    ),
                ),
            ),
        );*/
    }

    public function currentMenu() {

        $page = isset($_GET[$this->page_code]) ? $_GET[$this->page_code] : 'dash';

        if( !$page ) {

            $list = $this->tmpls;
            $page = key( $list );
        }

        return $this->current = $page;
    }

    public function create( $content ) {

        $this->header();

        echo $content;

        $this->footer();
    }

    /**
     *	additional sidebar
     */
    private function sidebar( ) {

        ?>

        <div id="app-sidebar-back"></div>
        <div id="app-sidebar-wrap">
            <?php echo $this->createMenu() ?>
        </div>

    <?php

    }

    private function header() {
        ?>
        <div class="wrap">
            <?php $this->sidebar() ?>

        <div class="page-content">

            <h2><span class="fa fa-<?php echo $this->icon ?>"></span> <?php echo $this->title ?> <?php do_action('sr_action_' . $this->current)?></h2>
            <div class="description"><?php echo $this->description ?></div>
            <div class="content-main">
                <?php

                if( $this->message != '' )
                    printf('<div class="alert alert-%s alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="%s">
                                <span aria-hidden="true">&times;</span></button>
                                %s
                            </div>',
                        $this->message_type, __('Close', 'sr'), $this->message
                    );
    }

    /**
     * Admin form after
     */
    private function footer() {
            ?>
                    </div>
                </div>
            </div>
        <?php
    }

    /**
     *	walker to list menu
     */
    private function createMenu() {

        $current 	= $this->current;
        $items 		= $this->menu;

        $return = "<ul>";

        foreach( $items as $slug => $args ) {

            $sub_result = $class = '';
            $sub_current = false;

            $class = ( $current == $slug ) ? 'item-active' : '';

            if( isset($args['submenu']) && count( $args['submenu'] ) > 0 ) {

                $sub_result = '<ul class="sub-item">';

                foreach( $args['submenu'] as $sub_slug => $sub_args ) {

                    $sub_class = ( $current == $sub_slug ) ? 'item-active' : '';

                    if( $current == $sub_slug ) $sub_current = true;

                    $sub_result .= '<li><a href="' . admin_url( 'admin.php?page=' . $this->page . '&' . $this->page_var . '=' . $sub_slug ) . '" class="' . $sub_class . '">' . $sub_args['title'] . '</a></li>';
                }

                $sub_result .= '</ul>';
            }

            if($sub_current) $class = 'item-active';

            $return .= '<li class="' . $class . '"><a href="' . admin_url( 'admin.php?page=' . $this->page . '&' . $this->page_var . '=' . $slug ) . '" class="' . $class . '"><span class="fa fa-' . $args['icon'] . '"></span> ' . $args['title']  . '</a>' . $sub_result . '</li>';
            if( isset($args['separator']) )
                $return .= '<li class="separator separator-' . $args['separator'] . '"></li>';
        }

        $return .= "</ul>";

        return $return;
    }

	public function textFields( $label = '', $args = array(), $layout = true ) {

		$result = '<tr valign="top">
					    <th scope="row">
						    <label for="">' . $label . ':</label>
					    </th><td>';

        if( count($args) > 0 ) foreach( $args as $key => $val ) {

            $result .= '<label for="' . $val['id'] . '"><input id="' . $val['id'] . '" type="text" value="' .
                $val['value'] . '" name="' . $key . '" class="' . $val['class'] . '"> ' . $val['label'] . '</label> ';
        }

        $result .= '</td></tr>';

		if( $layout )
			echo $result;

		return $result;

	}

	public function textField( $name = '', $args = array(), $layout = true ){

		$defaults = array(
			'label'         => '',
			'placeholder'   => '',
			'id'            => '',
			'value'         => '',
			'description'   => '',
			'readonly'      => false,
			'class'         => 'large-text',
			'maxlength'     => '',
            'text_after'    => '',
            'type'          => 'text'
		);

		$args = wp_parse_args( $args, $defaults );

		$readonly = ($args['readonly']) ? 'readonly="readonly"' : '';

		$result = '<tr valign="top">
					    <th scope="row">
						    <label for="' . $args['id'] . '">' . $args['label'] . ':</label>
					    </th>
					    <td>
						    <input id="' . $args['id'] . '" type="' . $args['type'] . '" value="' . $args['value'] .
                            '" name="' . $name . '" class="' . $args['class'] . '" ' . $readonly . ' placeholder="' . $args['placeholder'] . '">
						    ' . $args['text_after'] . '
						    <p class="description">' . $args['description'] . '</p>
					    </td>
				    </tr>';

		if( $layout )
			echo $result;

		return $result;

	}

	public function textTextArea( $name = '', $args = array(), $layout = true ){

		$defaults = array(
			'label'         => '',
			'id'            => '',
			'value'         => '',
			'description'   => '',
			'readonly'      => false,
			'rows'          => 2,
			'class'         => 'large-text'
		);

		$args = wp_parse_args( $args, $defaults );

		$readonly = ($args['readonly']) ? 'readonly="readonly"' : '';

		$result = '<tr valign="top">
					    <th scope="row">
						    <label for="' . $args['id'] . '">' . $args['label'] . ':</label>
					    </th>
					    <td>
					        <textarea rows="' . $args['rows'] . '" id="' . $args['id'] . '" class="' . $args['class'] . '" name="' . $name . '" ' . $readonly . '>' . $args['value'] . '</textarea>
						    <p class="description">' . $args['description'] . '</p>
					    </td>
				    </tr>';

		if( $layout )
			echo $result;

		return $result;

	}

	public function dropDownField( $name = '', $args = array(), $layout = true ){

		$defaults = array(
			'label'         => '',
			'selected'      => '',
			'id'            => '',
			'values'        => array(),
			'description'   => '',
			'readonly'      => false,
			'class'         => 'large-select',
		);

		$args = wp_parse_args( $args, $defaults );

		$readonly = ($args['readonly']) ? 'readonly="readonly"' : '';

		$result = '<tr valign="top">
					    <th scope="row">
						    <label for="' . $args['id'] . '">' . $args['label'] . ':</label>
					    </th>
					    <td>
						    <select id="' . $args['id'] . '" name="' . $name . '" class="' . $args['class'] . '" ' . $readonly . '>';

		if( count($args['values']) )
			foreach( $args['values'] as $key => $val ) {

				$selected = $key == $args['selected'] ? 'selected="selected"' : '';

				$result .= '<option value="' . $key . '" ' . $selected . '>' . $val . '</option>';
			}

		$result .= '</select>
		            <p class="description">' . $args['description'] . '</p>
					    </td>
				    </tr>';

		if($layout)
			echo $result;

		return $result;
	}

	public function checked( $name = '', $args = array(), $layout = true ) {

		$defaults = array(
			'id'            => '',
			'value'         => '',
			'type'          => 'checkbox',
			'readonly'      => false,
			'class'         => '',
			'label'         => __('Select to Enable', 'sr'),
			'title'         => __('Position', 'sr'),
            'checked'       => false
		);

		$args = wp_parse_args( $args, $defaults );

		$readonly = ($args['readonly']) ? 'readonly="readonly"' : '';
		$checked = ($args['checked']) ? 'checked="checked"' : '';

		$result = '<tr>
                    <th scope="row">' . $args['title'] . '</th>
                    <td><fieldset>
                        <legend class="screen-reader-text"><span>' . $args['title'] . '</span></legend>
                        <label for="' . $args['id'] . '">
                            <input name="' . $name . '" type="' . $args['type'] . '"
                            id="' . $args['id'] . '" class="' . $args['class'] . '" value="' . $args['value'] . '"
                            ' . $checked . ' ' . $readonly . '>
                            ' . $args['label'] . '</label>
                    </fieldset></td>
                    </tr>';

		if($layout)
			echo $result;

		return $result;
	}

	public function btn( $name = '', $args = array(), $layout = true ) {

		$defaults = array(
			'id'            => '',
			'value'         => __('Submit', 'sr'),
			'type'          => 'submit',
			'readonly'      => false,
			'class'         => 'button button-primary button-large',
		);

		$args = wp_parse_args( $args, $defaults );

		$readonly = ($args['readonly']) ? 'readonly="readonly"' : '';

		$result = '<tr valign="top">
					    <td colspan="2">
						    <input type="' . $args['type'] . '" name="' . $name . '" id="' . $args['id'] . '"
						    class="' . $args['class'] . '" ' . $readonly . ' value="' . $args['value'] . '"></td></tr>';
		if($layout)
			echo $result;

		return $result;
	}

	public function btns( $args = array(), $layout = true ){

		$defaults = array(
			'id'            => '',
			'name'          => '',
			'value'         => __('Submit', 'sr'),
			'type'          => 'submit',
			'readonly'      => false,
			'class'         => 'button button-primary button-large',
		);

        $count = count($args);

        if( $count == 0 ) return false;

        $result = '<tr valign="top"><td colspan="' . $count . '">';


        foreach( $args as $key => $val ){

            $val = wp_parse_args( $val, $defaults );
			
            $readonly = ($val['readonly']) ? 'readonly="readonly"' : '';

            if( $val['type'] == 'hidden' ) {
                $result .= '';
            }
            elseif( $val['type'] == 'a' ){
                $result .= $val['value'];
            }
            else{
                $result .= '<input type="' . $val['type'] . '" name="' . $val['name'] . '" id="' . $val['id'] . '"
						    class="' . $val['class'] . '" ' . $readonly . ' value="' . $val['value'] . '">';
            }
            $result .= ' ';
        }

        $result .= '</td></tr>';

		if($layout)
			echo $result;

		return $result;
	}
}