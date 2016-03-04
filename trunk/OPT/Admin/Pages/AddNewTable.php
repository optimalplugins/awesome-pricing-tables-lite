<?php

if (!defined('WPINC')) {
    die();
}

class OPT_Admin_Pages_AddNewTable
{
    private static $instance;
    private $table, $title, $tempates;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {
        global $wpdb;
        $this->table = $wpdb->prefix . 'optimal_pricing_tbl';
        $this->tempates = "";
    }

    public function onLoad()
    {
        wp_enqueue_style('jquery-ui');
        wp_enqueue_style('opt-pricing-tbl');
        wp_enqueue_style('font-awesome');
        wp_enqueue_style('select2');
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-widget');
        wp_enqueue_script('jquery-ui-mouse');
        wp_enqueue_script('jquery-ui-slider');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('select2');
        wp_enqueue_script('js-color');
        //wp_enqueue_script('opt-pricing-tbl-template');
        wp_enqueue_script('opt-pricing-tbl');
        wp_enqueue_script('opt-pricing-tbl-main');
    }

    public function mainDiv()
    {
        if (isset($_GET['page']) && $_GET['page'] === 'optimal-pricing-tbl-new') {
            ob_start();
            if (isset($_GET['edit']) && is_numeric($_GET['edit']) && $_GET['edit'] > 0) {
                $this->title = __('Edit Pricing Table', OptimalPricingTable::getTD());
                $edit = intval($_GET['edit']);
                $this->getHtml($edit);
            } elseif (isset($_GET['delete']) && is_numeric($_GET['delete']) && $_GET['delete'] > 0) {
                //delete pricing table
                $this->title = __('Delete Pricing Table', OptimalPricingTable::getTD());
                $this->deleteTable(intval($_GET['delete']));
            } else {
                //add new table
                $this->title = __('New Pricing Table', OptimalPricingTable::getTD());
                $this->getHtml();
            }
            $data = ob_get_clean();
            $this->getHeader();
            echo $data;
            $this->getFooter();
        }
    }

    private function deleteTable($id = 0)
    {
        global $wpdb;
        if (!empty($_POST) && check_admin_referer('delete_pricing_table_' . $id, 'delete_priging_tbl')) {
            $where = array('id' => $id);
            $where_format = array('%d');
            if ($wpdb->delete($this->table, $where, $where_format)) {
                $delete_message = __('Table Deleted Successfully.', OptimalPricingTable::getTD());
                $link = add_query_arg(array('page' => 'optimal-pricing-tbl', 'message' => $delete_message), admin_url('admin.php'));
                echo "<script>window.location = '$link';</script>";
            }
        } else {
            ?>
            <form method="post">
                <?php wp_nonce_field('delete_pricing_table_' . $id, 'delete_priging_tbl'); ?>
                <p>Are you sure you want to delete this? </p>

                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary"
                           value="<?php if (isset($_REQUEST['edit'])) {
                               echo "Edit Batch";
                           } else {
                               echo "Delete Table";
                           } ?>"/>
                    <?php echo '<a href="' . add_query_arg(array(), admin_url('admin.php?page=optimal-pricing-tbl')) . '" class="button button-secondary">Back</a>'; ?>
                </p>
            </form>
        <?php
        }
    }

    private function getHtml($edit = 0)
{
    global $wpdb;
    $select_default = '';
    if (is_numeric($edit) && $edit > 0) {
        $query = $wpdb->prepare('SELECT * FROM `' . $this->table . '` WHERE id=%d', array($edit));
        $row = $wpdb->get_row($query, ARRAY_A);
        if (isset($row['extras'])) {
            $extras = maybe_unserialize($row['extras']);
        }
    } else {
        $extras = array();
    }
    ob_start();
    include 'GlobalCSS.php';
    $global_css = ob_get_clean();

    if ($edit == 0) {
    ?>
    <script>
        $(document).ready(function() {
            presets.loadPreset('preset1', true);
        });
    </script>
    <?php
    }
    ?>
    <style type='text/css'>
        <?php echo $global_css; ?>
    </style>
    <div id="title">
        <input type="text" placeholder="Enter Table Name Here" autocomplete="off" spellcheck="true" id="post_title"
               size="25" maxlength="50" name="post_title"
               value="<?php if (isset($row['title']) && $row['title'] != '') {
                   echo $row['title'];
               } ?>">
    </div>
    <div class="optimal-pricing-table main-content">
        <form method="post" id="mainForm">
            <div>

                <div class="settings-area">
                    <ul class="nav">
                        <li id="templates-tab" class="active">
                            <h3 class="setting-label">Table Styles<i class="fa fa-chevron-down"></i></h3>

                            <div id="templates-tab-div" class="nav-div" style="display: none;">
                                <select name="preset" class="presets" style="width:100%">
                                    <option value="preset1">Style 01</option>
                                </select>
                            </div>
                        </li>

                        <li id="config-tab">
                            <h3 class="setting-label">Table<i class="fa fa-chevron-down"></i></h3>

                            <div id="config-tab-div" class="nav-div" style="display: none;">
                                <h3>General</h3>

                                <p><label for="margin">Margin</label></p>

                                <p></p>

                                <div id="marginSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 30%;"></a></div>
                                <input type="text" data-lineheight="1" data-max="10" data-min="0"
                                       value="<?php echo isset($extras['margin']) ? $extras['margin'] : 0; ?>"
                                       data-value="<?php echo isset($extras['margin']) ? $extras['margin'] : 0; ?>"
                                       class="slider" id="margin" name="margin">px<p></p>

                                <p><label for="padding">Padding</label></p>

                                <p></p>

                                <div id="paddingSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 50%;"></a></div>
                                <input type="text" data-lineheight="1" data-max="10" data-min="0"
                                       value="<?php echo isset($extras['padding']) ? $extras['padding'] : 0; ?>"
                                       data-value="<?php echo isset($extras['padding']) ? $extras['padding'] : 0; ?>"
                                       class="slider" id="padding" name="padding">px<p></p>
                                </p></p>

                                <br>

                                <p><label for="hoverEffectCheckbox">Enable Hover Effect</label>
                                    <input type="checkbox" id="hoverEffectCheckbox" name="hoverEffectCheckbox"
                                           value="yes" <?php echo isset($extras['hoverEffectCheckbox']) && $extras['hoverEffectCheckbox'] == 'no' ? '' : 'checked'; ?>
                                </p>

                                <hr>

                                <h3>Row</h3>

                                <a class="addRow clickable button">Add Row</a>
                                <select id="removeRow" class="control" style="display:inline-block">
                                    <option>Remove Row</option>
                                    <?php
                                    if (isset($row) && $row['html'] != ""):
                                        $dom = new DOMDocument;
                                        $dom->loadHTML($row['html']);
                                        foreach ($dom->getElementsByTagName('ul') as $ul) {
                                            $count = $ul->getElementsByTagName('li')->length;
                                            for ($i = 2; $i < $count; $i++):
                                                echo "<option value='{$i}'>Row {$i}</option>";
                                            endfor;
                                            break;
                                        }
                                    else:
                                        ?>
                                        <option value="2">Remove Row 2</option>
                                        <option value="3">Remove Row 3</option>
                                        <option value="4">Remove Row 4</option>
                                        <option value="5">Remove Row 5</option>
                                    <?php endif; ?>
                                </select>

                                <p><label for="priceOptions">Price Row</label>
                                    <select id="priceOptions" name="priceOptions">
                                        <?php
                                        if (isset($row) && $row['html'] != ""):
                                            $dom = new DOMDocument;
                                            $dom->loadHTML($row['html']);
                                            foreach ($dom->getElementsByTagName('ul') as $ul) {
                                                $count = $ul->getElementsByTagName('li')->length;
                                                for ($i = 2; $i < $count; $i++):
                                                    $selected = isset($extras['priceOptions']) && $extras['priceOptions'] == $i ? 'selected="selected"' : '';
                                                    echo "<option value='{$i}' $selected>Row {$i}</option>";
                                                endfor;
                                                break;
                                            }
                                        else:
                                            ?>
                                            <option value="2">Row 2</option>
                                            <option value="3">Row 3</option>
                                            <option value="4">Row 4</option>
                                        <?php endif; ?>
                                    </select>
                                    <br>

                                <hr>
                                <h3>Column</h3>

                                <a class="addColumn clickable control button">Add Col</a>

                                <select id="removeColumn" class="control" style="display:inline-block">
                                    <option selected="selected">Remove Column</option>
                                    <?php
                                    if (isset($row) && $row['html'] != ""):
                                        $dom = new DOMDocument;
                                        $dom->loadHTML($row['html']);
                                        $count = $dom->getElementsByTagName('ul')->length;
                                        for ($i = 1; $i <= $count; $i++):
                                            echo "<option value='$i'>Col $i</option>";
                                        endfor;
                                    else:
                                        ?>
                                        <option value="1">Remove Col 1</option>
                                        <option value="2">Remove Col 2</option>
                                        <option value="3">Remove Col 3</option>
                                    <?php endif; ?>
                                </select>

                                <p><label for="featureColumn">Featured Col</label>
                                    <select id="featureColumn" name="featureColumn">
                                        <option value="">None</option>
                                        <?php
                                        if (isset($row) && $row['html'] != ""):
                                            $dom = new DOMDocument;
                                            $dom->loadHTML($row['html']);
                                            $count = $dom->getElementsByTagName('ul')->length;
                                            for ($i = 1; $i <= $count; $i++):
                                                $selected = isset($extras['featureColumn']) && ($extras['featureColumn'] == $i) ? 'selected="selected"' : '';
                                                echo "<option value='$i' $selected>Col $i</option>";
                                            endfor;
                                        else:
                                            ?>
                                            <option value="1">Col 1</option>
                                            <option value="2">Col 2</option>
                                            <option value="3" selected="selected">Col 3</option>
                                            <option value="4">Col 4</option>
                                        <?php endif; ?>
                                    </select>
                                </p>

                                <hr>

                            </div>
                        </li>

                        <li id="style-tab">
                            <h3 class="setting-label">Colors<i class="fa fa-chevron-down"></i></h3>

                            <div id="style-tab-div" class="nav-div" style="display: none;">
                                <h3>Header</h3>

                                <p><label for="headingTextColour">Text</label>

                                    <input
                                        value="<?php echo isset($extras['headingTextColour']) && $extras['headingTextColour'] != '' ? $extras['headingTextColour'] : '#FFFFFF'; ?>"
                                        id="headingTextColour" name="headingTextColour"
                                        class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);'}"
                                        style="background-image: none; background-color: '<?php echo isset($extras['headingTextColour']) && $extras['headingTextColour'] != '' ? $extras['headingTextColour'] : '#FFFFFF'; ?>'; color: rgb(0, 0, 0);"
                                        autocomplete="off"></p>

                                <p><label for="backgroundColour1">BG Color Start</label>

                                    <input
                                        value="<?php echo isset($extras['backgroundColour1']) && $extras['backgroundColour1'] != '' ? $extras['backgroundColour1'] : '#DF5D5D'; ?>"
                                        id="backgroundColour1" name="backgroundColour1"
                                        class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);'}"
                                        style="background-image: none; background-color: '<?php echo isset($extras['backgroundColour1']) && $extras['backgroundColour1'] != '' ? $extras['backgroundColour1'] : '#DF5D5D'; ?>'; color: rgb(255, 255, 255);"
                                        autocomplete="off"></p>

                                <p><label for="backgroundColour2">BG Color End</label>

                                    <input
                                        value="<?php echo isset($extras['backgroundColour2']) && $extras['backgroundColour2'] != '' ? $extras['backgroundColour2'] : ''; ?>"
                                        id="backgroundColour2" name="backgroundColour2"
                                        class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);', required:false}"
                                        autocomplete="off"></p>

                                <hr>

                                <h3>Pricing</h3>

                                <p><label for="pricingTextColour">Text</label>
                                    <input
                                        value="<?php echo isset($extras['pricingTextColour']) && $extras['pricingTextColour'] != '' ? $extras['pricingTextColour'] : ''; ?>"
                                        id="pricingTextColour" name="pricingTextColour"
                                        class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);', required:false}"
                                        autocomplete="off"></p>

                                <p><label for="pricingBackgroundColour1">BG Color Start</label>
                                    <input
                                        value="<?php echo isset($extras['pricingBackgroundColour1']) && $extras['pricingBackgroundColour1'] != '' ? $extras['pricingBackgroundColour1'] : ''; ?>"
                                        id="pricingBackgroundColour1" name="pricingBackgroundColour1"
                                        class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);', required:false}"
                                        autocomplete="off"></p>

                                <p><label for="pricingBackgroundColour2">BG Color End</label>
                                    <input
                                        value="<?php echo isset($extras['pricingBackgroundColour2']) && $extras['pricingBackgroundColour2'] != '' ? $extras['pricingBackgroundColour2'] : ''; ?>"
                                        id="pricingBackgroundColour2" name="pricingBackgroundColour2"
                                        class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);', required:false}"
                                        autocomplete="off"></p>

                                <hr>

                                <h3>Item</h3>

                                <p><label for="itemTextColour">Text</label>
                                    <input
                                        value="<?php echo isset($extras['itemTextColour']) && $extras['itemTextColour'] != '' ? $extras['itemTextColour'] : '#FFFFFF'; ?>"
                                        id="itemTextColour" name="itemTextColour"
                                        class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);'}"
                                        style="background-image: none; background-color: '<?php echo isset($extras['itemTextColour']) && $extras['itemTextColour'] != '' ? $extras['itemTextColour'] : '#FFFFFF'; ?>'; color: rgb(0, 0, 0);"
                                        autocomplete="off"></p>

                                <p><label for="rowBackgroundColour">BG Color 1</label>
                                    <input
                                        value="<?php echo isset($extras['rowBackgroundColour']) && $extras['rowBackgroundColour'] != '' ? $extras['rowBackgroundColour'] : '#EFEFEF'; ?>"
                                        id="rowBackgroundColour" name="rowBackgroundColour"
                                        class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);'}"
                                        style="background-image: none; background-color: '<?php echo isset($extras['rowBackgroundColour']) && $extras['rowBackgroundColour'] != '' ? $extras['rowBackgroundColour'] : '#EFEFEF'; ?>'; color: rgb(0, 0, 0);"
                                        autocomplete="off"></p>

                                <p><label for="alternateRowBackgroundColour">BG Color 2</label>
                                    <input
                                        value="<?php echo isset($extras['alternateRowBackgroundColour']) && $extras['alternateRowBackgroundColour'] != '' ? $extras['alternateRowBackgroundColour'] : '#F7F7F7'; ?>"
                                        id="alternateRowBackgroundColour" name="alternateRowBackgroundColour"
                                        class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);'}"
                                        style="background-image: none; background-color: '<?php echo isset($extras['alternateRowBackgroundColour']) && $extras['alternateRowBackgroundColour'] != '' ? $extras['alternateRowBackgroundColour'] : '#F7F7F7'; ?>'; color: rgb(0, 0, 0);"
                                        autocomplete="off"></p>

                                <p><label for="columnBackgroundColour">Column BG Color</label>
                                    <input
                                        value="<?php echo isset($extras['columnBackgroundColour']) && $extras['columnBackgroundColour'] != '' ? $extras['columnBackgroundColour'] : '#FFFFFF'; ?>"
                                        id="columnBackgroundColour" name="columnBackgroundColour"
                                        class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);'}"
                                        style="background-image: none; background-color: '<?php echo isset($extras['columnBackgroundColour']) && $extras['columnBackgroundColour'] != '' ? $extras['columnBackgroundColour'] : '#FFFFFF'; ?>'; color: rgb(0, 0, 0);"
                                        autocomplete="off"></p>

                                <hr>
                                <h3>Call To Action</h3>

                                <p><label for="callToActionBackgroundColour1">BG Color Start</label>
                                    <input
                                        value="<?php echo isset($extras['callToActionBackgroundColour1']) && $extras['callToActionBackgroundColour1'] != '' ? $extras['callToActionBackgroundColour1'] : ''; ?>"
                                        id="callToActionBackgroundColour1" name="callToActionBackgroundColour1"
                                        class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);'}"
                                        style="background-image: none; background-color: '<?php echo isset($extras['buttonRowBackgroundColour1']) && $extras['buttonRowBackgroundColour1'] != '' ? $extras['buttonRowBackgroundColour1'] : ''; ?>'; color: rgb(0, 0, 0);"
                                        autocomplete="off"></p>

                                <p><label for="callToActionBackgroundColour2">BG Color End</label>
                                    <input
                                        value="<?php echo isset($extras['callToActionBackgroundColour2']) && $extras['callToActionBackgroundColour2'] != '' ? $extras['callToActionBackgroundColour2'] : ''; ?>"
                                        id="callToActionBackgroundColour2" name="callToActionBackgroundColour2"
                                        class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);'}"
                                        style="background-image: none; background-color: '<?php echo isset($extras['buttonRowBackgroundColour2']) && $extras['buttonRowBackgroundColour2'] != '' ? $extras['buttonRowBackgroundColour2'] : ''; ?>'; color: rgb(0, 0, 0);"
                                        autocomplete="off"></p>

                                <hr>
                                <h3>Button</h3>

                                <p><label for="buttonTextColour">Text</label>
                                    <input
                                        value="<?php echo isset($extras['buttonTextColour']) && $extras['buttonTextColour'] != '' ? $extras['buttonTextColour'] : '#000000'; ?>"
                                        id="buttonTextColour" name="buttonTextColour"
                                        class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);'}"
                                        style="background-image: none; background-color:'<?php echo isset($extras['buttonTextColour']) && $extras['buttonTextColour'] != '' ? $extras['buttonTextColour'] : '#000000'; ?>'; color: rgb(255, 255, 255);"
                                        autocomplete="off"></p>

                                <p><label for="buttonBackgroundColour1">BG Color Start</label>
                                    <input
                                        value="<?php echo isset($extras['buttonBackgroundColour1']) && $extras['buttonBackgroundColour1'] != '' ? $extras['buttonBackgroundColour1'] : ''; ?>"
                                        id="buttonBackgroundColour1" name="buttonBackgroundColour1"
                                        class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);'}"
                                        style="background-image: none; background-color: '<?php echo isset($extras['buttonBackgroundColour1']) && $extras['buttonBackgroundColour1'] != '' ? $extras['buttonBackgroundColour1'] : ''; ?>'; color: rgb(0, 0, 0);"
                                        autocomplete="off"></p>

                                <p><label for="buttonBackgroundColour2">BG Color End</label>
                                    <input
                                        value="<?php echo isset($extras['buttonBackgroundColour2']) && $extras['buttonBackgroundColour2'] != '' ? $extras['buttonBackgroundColour2'] : ''; ?>"
                                        id="buttonBackgroundColour2" name="buttonBackgroundColour2"
                                        class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);'}"
                                        style="background-image: none; background-color: '<?php echo isset($extras['buttonBackgroundColour2']) && $extras['buttonBackgroundColour2'] != '' ? $extras['buttonBackgroundColour2'] : ''; ?>'; color: rgb(0, 0, 0);"
                                        autocomplete="off"></p>

                                <p><label for="buttonBorderColour">Border Color</label>
                                    <input
                                        value="<?php echo isset($extras['buttonBorderColour']) && $extras['buttonBorderColour'] != '' ? $extras['buttonBorderColour'] : '#B83737'; ?>"
                                        id="buttonBorderColour" name="buttonBorderColour"
                                        class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);'}"
                                        style="background-image: none; background-color: '<?php echo isset($extras['buttonBorderColour']) && $extras['buttonBorderColour'] != '' ? $extras['buttonBorderColour'] : '#b5b5b5'; ?>'; color: rgb(255, 255, 255);"
                                        autocomplete="off"></p>

                                <hr>
                            </div>
                        </li>

                        <li id="text-tab">
                            <h3 class="setting-label">Fonts<i class="fa fa-chevron-down"></i></h3>

                            <div id="text-tab-div" class="nav-div" style="display: none;">
                                <h3>General</h3>

                                <p><label for="fontFamily">Font Family</label>
                                    <?php
                                    $data = array(
                                        'Arial' => 'Arial', 'Courier New' => 'Courier New', 'Georgia' => 'Georgia',
                                        'Impact' => 'Impact', 'Times New Roman' => 'Times New Roman', 'Trebuchet MS' => 'Trebuchet MS',
                                        'Verdana' => 'Verdana', 'Open Sans' => 'Open Sans', 'Oswald' => 'Oswald',
                                        'Roboto' => 'Roboto', 'Droid Sans' => 'Droid Sans', 'Lato' => 'Lato',
                                        'pen Sans Condensed' => 'pen Sans Condensed', 'PT Sans' => 'PT Sans', 'Droid Serif' => 'Droid Serif',
                                        'Ubuntu' => 'Ubuntu', 'PT Sans Narrow' => 'PT Sans Narrow', 'Source Sans Pro' => 'Source Sans Pro',
                                        'Roboto Condensed' => 'Roboto Condensed', 'Yanone Kaffeesatz' => 'Yanone Kaffeesatz', 'Lora' => 'Lora',
                                        'Oxygen' => 'Oxygen', 'Arvo' => 'Arvo', 'Raleway' => 'Raleway',
                                        'Lobster' => 'Lobster', 'Arimo' => 'Arimo', 'Rokkitt' => 'Rokkitt',
                                        'Montserrat' => 'Montserrat', 'Bitter' => 'Bitter', 'Nunito' => 'Nunito',
                                        'Francois One' => 'Francois One', 'Merriweather' => 'Merriweather', 'PT Serif' => 'PT Serif',
                                        'Cabin' => 'Cabin', 'Libre Baskerville' => 'Libre Baskerville', 'Abel' => 'Abel',
                                        'Crafty Girls' => 'Crafty Girls'
                                    );
                                    $other = array(
                                        'name' => 'fontFamily', 'id' => 'fontFamily', 'class' => ''
                                    );
                                    if (isset($extras['fontFamily']) && $extras['fontFamily'] != '')
                                        $other['selected'] = $extras['fontFamily'];

                                    $other['option'] = '<option value="">Select Font</option>';
                                    echo $this->selectHtml($data, $other);
                                    ?>
                                </p>

                                <br>

                                <p><label for="textAlign">Text Align</label>
                                    <?php
                                    $data = array(
                                        'left' => 'Left', 'right' => 'Right', 'center' => 'Center'
                                    );
                                    $other = array(
                                        'name' => 'textAlign', 'id' => 'textAlign', 'class' => '',
                                        'selected' => 'center'
                                    );
                                    if (isset($extras['textAlign']) && $extras['textAlign'] != '')
                                        $other['selected'] = $extras['textAlign'];

                                    echo $this->selectHtml($data, $other);
                                    ?>
                                </p>

                                <br>

                                <p><label for="columnWidth">Line Height</label></p>

                                <p></p>

                                <div id="lineHeightSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 15%;"></a></div>
                                <input type="text" data-lineheight="1" data-max="50" data-min="10"
                                       value="<?php echo isset($extras['lineHeight']) ? $extras['lineHeight'] : 16; ?>"
                                       data-value="<?php echo isset($extras['lineHeight']) ? $extras['lineHeight'] : 16; ?>"
                                       class="slider" id="lineHeight" name="lineHeight">px<p></p>

                                <hr>
                                <h3>Header</h3>

                                <p><label for="columnWidth">Font size</label></p>

                                <p></p>

                                <div id="headingFontSizeSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 15%;"></a></div>
                                <input type="text" data-lineheight="1" data-max="50" data-min="10"
                                       value="<?php echo isset($extras['headingFontSize']) ? $extras['headingFontSize'] : 16; ?>"
                                       data-value="<?php echo isset($extras['headingFontSize']) ? $extras['headingFontSize'] : 16; ?>"
                                       class="slider" id="headingFontSize" name="headingFontSize">px<p></p>

                                <hr>

                                <h3>Pricing</h3>

                                <p><label for="columnWidth">Font size</label></p>

                                <p></p>

                                <div id="pricingFontSizeSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 37.5%;"></a></div>
                                <input type="text" data-max="50" data-min="10"
                                       value="<?php echo isset($extras['pricingFontSize']) ? $extras['pricingFontSize'] : 25; ?>"
                                       data-value="<?php echo isset($extras['pricingFontSize']) ? $extras['pricingFontSize'] : 25; ?>"
                                       class="slider" id="pricingFontSize" name="pricingFontSize">px<p></p>

                                <hr>

                                <h3>Item</h3>

                                <p><label for="columnWidth">Font size</label></p>

                                <p></p>

                                <div id="rowFontSizeSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 10%;"></a></div>
                                <input type="text" data-max="50" data-min="10"
                                       value="<?php echo isset($extras['rowFontSize']) ? $extras['rowFontSize'] : 14; ?>"
                                       data-value="<?php echo isset($extras['rowFontSize']) ? $extras['rowFontSize'] : 14; ?>"
                                       class="slider" id="rowFontSize" name="rowFontSize">px<p></p>

                                <hr>

                                <h3>Call To Action</h3>

                                <p><label for="columnWidth">Font size</label></p>

                                <p></p>

                                <div id="buttonFontSizeSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 10%;"></a></div>
                                <input type="text" data-max="50" data-min="10"
                                       value="<?php echo isset($extras['buttonFontSize']) ? $extras['buttonFontSize'] : 14; ?>"
                                       data-value="<?php echo isset($extras['buttonFontSize']) ? $extras['buttonFontSize'] : 14; ?>"
                                       class="slider" id="buttonFontSize" name="buttonFontSize">px<p></p>
                                <hr>
                            </div>
                        </li>

                        <li id="layout-tab">
                            <h3 class="setting-label">Height & Width<i class="fa fa-chevron-down"></i></h3>

                            <div id="layout-tab-div" class="nav-div" style="display: none;">
                                <h3>Header</h3>

                                <p><label for="headingCellHeight">Height</label></p>

                                <p></p>

                                <div id="headingCellHeightSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 25%;"></a></div>
                                <input type="text" data-max="350" data-min="10"
                                       value="<?php echo isset($extras['headingCellHeight']) ? $extras['headingCellHeight'] : 40; ?>"
                                       data-value="<?php echo isset($extras['headingCellHeight']) ? $extras['headingCellHeight'] : 40; ?>"
                                       class="slider" id="headingCellHeight" name="headingCellHeight">px<p></p>

                                <p><label for="headingCellPaddingTop">Padding Top</label></p>

                                <p></p>

                                <div id="headingCellPaddingTopSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 5%;"></a></div>
                                <input type="text" data-max="350" data-min="0"
                                       value="<?php echo isset($extras['headingCellPaddingTop']) ? $extras['headingCellPaddingTop'] : 5; ?>"
                                       data-value="<?php echo isset($extras['headingCellPaddingTop']) ? $extras['headingCellPaddingTop'] : 5; ?>"
                                       class="slider" id="headingCellPaddingTop"
                                       name="headingCellPaddingTop">px<p></p>

                                <hr>

                                <h3>Pricing</h3>

                                <p><label for="pricingCellHeight">Height</label></p>

                                <p></p>

                                <div id="pricingCellHeightSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 25%;"></a></div>
                                <input type="text" data-max="350" data-min="10"
                                       value="<?php echo isset($extras['pricingCellHeight']) ? $extras['pricingCellHeight'] : 40; ?>"
                                       data-value="<?php echo isset($extras['pricingCellHeight']) ? $extras['pricingCellHeight'] : 40; ?>"
                                       class="slider" id="pricingCellHeight" name="pricingCellHeight">px<p></p>

                                <p><label for="pricingCellPaddingTop">Padding Top</label></p>

                                <p></p>

                                <div id="pricingCellPaddingTopSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 5%;"></a></div>
                                <input type="text" data-max="350" data-min="0"
                                       value="<?php echo isset($extras['pricingCellPaddingTop']) ? $extras['pricingCellPaddingTop'] : 5; ?>"
                                       data-value="<?php echo isset($extras['pricingCellPaddingTop']) ? $extras['pricingCellPaddingTop'] : 5; ?>"
                                       class="slider" id="pricingCellPaddingTop"
                                       name="pricingCellPaddingTop">px<p></p>

                                <hr>

                                <h3>Item</h3>

                                <p><label for="cellHeight">Height</label></p>

                                <p></p>

                                <div id="cellHeightSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 25%;"></a></div>
                                <input type="text" data-max="80" data-min="10"
                                       value="<?php echo isset($extras['cellHeight']) ? $extras['cellHeight'] : 40; ?>"
                                       data-value="<?php echo isset($extras['cellHeight']) ? $extras['cellHeight'] : 40; ?>"
                                       class="slider" id="cellHeight" name="cellHeight">px<p></p>

                                <p><label for="cellPaddingTop">Padding Top</label></p>

                                <p></p>

                                <div id="cellPaddingTopSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 50%;"></a></div>
                                <input type="text" data-lineheight="1" data-max="50" data-min="0"
                                       value="<?php echo isset($extras['cellPaddingTop']) ? $extras['cellPaddingTop'] : 5; ?>"
                                       data-value="<?php echo isset($extras['cellPaddingTop']) ? $extras['cellPaddingTop'] : 5; ?>"
                                       class="slider" id="cellPaddingTop" name="cellPaddingTop">px<p></p>


                                <p><label for="columnWidth">Width</label></p>

                                <p></p>

                                <div id="columnWidthSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 25%;"></a></div>
                                <input type="text" data-max="500" data-min="100"
                                       value="<?php echo isset($extras['columnWidth']) ? $extras['columnWidth'] : 200; ?>"
                                       data-value="<?php echo isset($extras['columnWidth']) ? $extras['columnWidth'] : 200; ?>"
                                       class="slider" id="columnWidth" name="columnWidth">px<p></p>

                                <hr>

                                <h3>Call to Action</h3>

                                <p><label for="callToActionCellHeight">Height</label></p>

                                <p></p>

                                <div id="callToActionCellHeightSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 25%;"></a></div>
                                <input type="text" data-max="350" data-min="10"
                                       value="<?php echo isset($extras['callToActionCellHeight']) ? $extras['callToActionCellHeight'] : 40; ?>"
                                       data-value="<?php echo isset($extras['callToActionCellHeight']) ? $extras['callToActionCellHeight'] : 40; ?>"
                                       class="slider" id="callToActionCellHeight" name="callToActionCellHeight">px<p></p>

                                <p><label for="callToActionCellPaddingTop">Padding Top</label></p>

                                <p></p>

                                <div id="callToActionCellPaddingTopSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 5%;"></a></div>
                                <input type="text" data-max="50" data-min="0"
                                       value="<?php echo isset($extras['callToActionCellPaddingTop']) ? $extras['callToActionCellPaddingTop'] : 5; ?>"
                                       data-value="<?php echo isset($extras['callToActionCellPaddingTop']) ? $extras['callToActionCellPaddingTop'] : 5; ?>"
                                       class="slider" id="callToActionCellPaddingTop"
                                       name="callToActionCellPaddingTop">px<p></p>

                                <hr>

                                <h3>Button</h3>

                                <p><label for="buttonBorderPaddingTopBottom">Top Bottom Padding</label></p>

                                <p></p>

                                <div id="buttonBorderPaddingTopBottomSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 5%;"></a></div>
                                <input type="text" data-max="30" data-min="0"
                                       value="<?php echo isset($extras['buttonBorderPaddingTopBottom']) ? $extras['buttonBorderPaddingTopBottom'] : 5; ?>"
                                       data-value="<?php echo isset($extras['buttonBorderPaddingTopBottom']) ? $extras['buttonBorderPaddingTopBottom'] : 5; ?>"
                                       class="slider" id="buttonBorderPaddingTopBottom"
                                       name="buttonBorderPaddingTopBottom">px<p></p>

                                <p><label for="buttonBorderPaddingLeftRight">Left Right Padding</label></p>

                                <p></p>

                                <div id="buttonBorderPaddingLeftRightSlider"
                                     class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                     aria-disabled="false"><a href="#"
                                                              class="ui-slider-handle ui-state-default ui-corner-all"
                                                              style="left: 15%;"></a></div>
                                <input type="text" data-max="60" data-min="0"
                                       value="<?php echo isset($extras['buttonBorderPaddingLeftRight']) ? $extras['buttonBorderPaddingLeftRight'] : 15; ?>"
                                       data-value="<?php echo isset($extras['buttonBorderPaddingLeftRight']) ? $extras['buttonBorderPaddingLeftRight'] : 15; ?>"
                                       class="slider" id="buttonBorderPaddingLeftRight"
                                       name="buttonBorderPaddingLeftRight">px<p></p>
                                <br>

                                <p><label for="buttonBorderStyle">Style</label>
                                    <?php
                                    $data = array(
                                        'solid' => 'Solid', 'dotted' => 'Dotted', 'dashed' => 'Dashed',
                                        'double' => 'Double', 'groove' => 'Groove', 'ridge' => 'Ridge',
                                        'inset' => 'Inset', 'outset' => 'Outset'
                                    );
                                    $other = array(
                                        'name' => 'buttonBorderStyle', 'id' => 'buttonBorderStyle', 'class' => ''
                                    );
                                    $other['option'] = '<option value="">None</option>';
                                    if (isset($extras['buttonBorderStyle']) && $extras['buttonBorderStyle'] != '') {
                                        $other['selected'] = $extras['buttonBorderStyle'];
                                    } else {
                                        $other['selected'] = 'solid';
                                    }
                                        echo $this->selectHtml($data, $other);
                                        ?>
                                    </p>
<br>
                                    <p><label for="buttonBorderRadius">Button Border Radius</label></p>

                                    <p></p>

                                    <div id="buttonBorderRadiusSlider"
                                         class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                         aria-disabled="false"><a href="#"
                                                                  class="ui-slider-handle ui-state-default ui-corner-all"
                                                                  style="left: 2%;"></a></div>
                                    <input type="text" data-max="30" data-min="0"
                                           value="<?php echo isset($extras['buttonBorderRadius']) ? $extras['buttonBorderRadius'] : 2; ?>"
                                           data-value="<?php echo isset($extras['buttonBorderRadius']) ? $extras['buttonBorderRadius'] : 2; ?>2"
                                           class="slider" id="buttonBorderRadius" name="buttonBorderRadius">px
                                    <p></p>

                                    <p><label for="buttonBorderWidth">Button Border Width</label></p>

                                    <p></p>

                                    <div id="buttonBorderWidthSlider"
                                         class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                         aria-disabled="false"><a href="#"
                                                                  class="ui-slider-handle ui-state-default ui-corner-all"
                                                                  style="left: 2%;"></a></div>
                                    <input type="text" data-max="30" data-min="0"
                                           value="<?php echo isset($extras['buttonBorderWidth']) ? $extras['buttonBorderWidth'] : 1; ?>"
                                           data-value="<?php echo isset($extras['buttonBorderWidth']) ? $extras['buttonBorderWidth'] : 1; ?>2"
                                           class="slider" id="buttonBorderWidth" name="buttonBorderWidth">px
                                    <p></p>

                                    <br>

                                    <label for="buttonShadowEffect">Shadow Effect</label>
                                    <input type="checkbox" id="buttonShadowEffect" name="buttonShadowEffect"
                                           value="yes" <?php echo isset($extras['buttonShadowEffect']) && $extras['buttonShadowEffect'] == 'no' ? '' : 'checked'; ?>>
                                    </p>

                                    <hr>
                                </div>
                            </li>

                            <li id="border-tab">
                                <h3 class="setting-label">Border<i class="fa fa-chevron-down"></i></h3>

                                <div id="border-tab-div" class="nav-div" style="display: none;">
                                    <h3>Main</h3>

                                    <p><label for="borderColour">Border Color</label><input
                                            value="<?php echo isset($extras['borderColour']) && $extras['borderColour'] != '' ? $extras['borderColour'] : '#CCCCCC'; ?>"
                                            id="borderColour" name="borderColour"
                                            class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);'}"
                                            style="background-image: none; background-color: '<?php echo isset($extras['borderColour']) && $extras['borderColour'] != '' ? $extras['borderColour'] : '#CCCCCC'; ?>'; color: rgb(0, 0, 0);"
                                            autocomplete="off"></p>

                                    <br>

                                    <p><label for="borderStyle">Style</label>
                                        <?php
                                        $data = array(
                                            'solid' => 'Solid', 'dotted' => 'Dotted', 'dashed' => 'Dashed',
                                            'double' => 'Double', 'groove' => 'Groove', 'ridge' => 'Ridge',
                                            'inset' => 'Inset', 'outset' => 'Outset'
                                        );
                                        $other = array(
                                            'name' => 'borderStyle', 'id' => 'borderStyle', 'class' => ''
                                        );
                                        $other['option'] = '<option value="">None</option>';
                                        if (isset($extras['borderStyle']) && $extras['borderStyle'] != '')
                                            $other['selected'] = $extras['borderStyle'];
                                        echo $this->selectHtml($data, $other);
                                        ?>
                                    </p>

                                    <br>

                                    <p><label for="borderWidth">Width</label></p>

                                    <p></p>

                                    <div id="borderWidthSlider"
                                         class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                         aria-disabled="false"><a href="#"
                                                                  class="ui-slider-handle ui-state-default ui-corner-all"
                                                                  style="left: 10%;"></a></div>
                                    <input type="text" data-max="10"
                                           value="<?php echo isset($extras['borderWidth']) ? $extras['borderWidth'] : 1; ?>"
                                           data-value="<?php echo isset($extras['borderWidth']) ? $extras['borderWidth'] : 1; ?>"
                                           class="slider" id="borderWidth" name="borderWidth">px<p></p>

                                    <p><label for="borderRadius">Radius</label></p>

                                    <p></p>

                                    <div id="borderRadiusSlider"
                                         class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                         aria-disabled="false"><a href="#"
                                                                  class="ui-slider-handle ui-state-default ui-corner-all"
                                                                  style="left: 3%;"></a></div>
                                    <input type="text"
                                           value="<?php echo isset($extras['borderRadius']) ? $extras['borderRadius'] : 3; ?>"
                                           data-value="<?php echo isset($extras['borderRadius']) ? $extras['borderRadius'] : 3; ?>"
                                           class="slider" id="borderRadius" name="borderRadius">px<p></p>

                                    <hr>

                                    <h3>Cell</h3>

                                    <p><label for="cellBorderColour">Border Color</label><input
                                            value="<?php echo isset($extras['cellBorderColour']) && $extras['cellBorderColour'] != '' ? $extras['cellBorderColour'] : '#B83737'; ?>"
                                            id="cellBorderColour" name="cellBorderColour"
                                            class="color {hash:true, pickerPosition:'right', onImmediateChange:'updateColourPickerValue(this);'}"
                                            style="background-image: none; background-color: rgb(184, 55, 55); color: '<?php echo isset($extras['cellBorderColour']) && $extras['cellBorderColour'] != '' ? $extras['cellBorderColour'] : '#B83737'; ?>';"
                                            autocomplete="off"></p>

                                    <br>

                                    <p><label for="cellBorderStyle">Style</label>
                                        <?php
                                        $data = array(
                                            'solid' => 'Solid', 'dotted' => 'Dotted', 'dashed' => 'Dashed',
                                            'double' => 'Double', 'groove' => 'Groove', 'ridge' => 'Ridge',
                                            'inset' => 'Inset', 'outset' => 'Outset'
                                        );
                                        $other = array(
                                            'name' => 'cellBorderStyle', 'id' => 'cellBorderStyle', 'class' => ''
                                        );
                                        $other['option'] = '<option value="">None</option>';
                                        if (isset($extras['cellBorderStyle']) && $extras['cellBorderStyle'] != '')
                                            $other['selected'] = $extras['cellBorderStyle'];

                                        echo $this->selectHtml($data, $other);
                                        ?>
                                    </p>

                                    <p><label for="cellBorderWidth">Width</label></p>

                                    <p></p>

                                    <div id="cellBorderWidthSlider"
                                         class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                         aria-disabled="false"><a href="#"
                                                                  class="ui-slider-handle ui-state-default ui-corner-all"
                                                                  style="left: 20%;"></a></div>
                                    <input type="text" data-max="10"
                                           value="<?php echo isset($extras['cellBorderWidth']) ? $extras['cellBorderWidth'] : 2; ?>"
                                           data-value="<?php echo isset($extras['cellBorderWidth']) ? $extras['cellBorderWidth'] : 2; ?>"
                                           class="slider" id="cellBorderWidth" name="cellBorderWidth">px<p></p>

                                    <p><label for="cellBottomBorderWidth">Bottom Width</label></p>

                                    <p></p>

                                    <div id="cellBottomBorderWidthSlider"
                                         class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                         aria-disabled="false"><a href="#"
                                                                  class="ui-slider-handle ui-state-default ui-corner-all"
                                                                  style="left: 20%;"></a></div>
                                    <input type="text" data-max="10"
                                           value="<?php echo isset($extras['cellBottomBorderWidth']) ? $extras['cellBottomBorderWidth'] : 2; ?>"
                                           data-value="<?php echo isset($extras['cellBottomBorderWidth']) ? $extras['cellBottomBorderWidth'] : 2; ?>"
                                           class="slider" id="cellBottomBorderWidth" name="cellBottomBorderWidth">px
                                    <p></p>

                                    <p><label for="cellBorderRadius">Cell Border Radius</label></p>

                                    <p></p>

                                    <div id="cellBorderRadiusSlider"
                                         class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                         aria-disabled="false"><a href="#"
                                                                  class="ui-slider-handle ui-state-default ui-corner-all"
                                                                  style="left: 0%;"></a></div>
                                    <input type="text"
                                           value="<?php echo isset($extras['cellBorderRadius']) ? $extras['cellBorderRadius'] : 0; ?>"
                                           data-value="<?php echo isset($extras['cellBorderRadius']) ? $extras['cellBorderRadius'] : 0; ?>"
                                           class="slider" id="cellBorderRadius" name="cellBorderRadius">px<p></p>
                                    <hr>

                                    <li id="custom-css-tab">
                                        <h3 class="setting-label">Custom CSS<i class="fa fa-chevron-down"></i></h3>

                                        <div id="custom-css-tab-div" class="nav-div" style="display: block;">
                                            <p><textarea style="width:100%" rows="5" name="extra_css"
                                                         id="extra_css"><?php echo isset($extras['extra_css']) ? $extras['extra_css'] : ''; ?></textarea>
                                            </p>
                                        </div>
                                    </li>

                        </ul>

                    </div>

                    <div class="preview-area">
                        <input type="hidden" value="none" id="list-style">
                        <input type="hidden" value="left" id="float">
                        <input type="hidden" value="0" id="padding">

                        <div class="preview-element-area">
                            <div class="button-panel">
                                <!--a id="showCtrlBtn" class="clickable control button button-primary">Hide Controls</a-->
                                <a href="#" class="button button-primary" id="add_new"><?php if ($edit > 0) {
                                        echo "Update Table";
                                    } else {
                                        echo "Publish Table";
                                    } ?></a>
                                <?php if ($edit === 0) { ?><a href="#" class="button button-primary"
                                                              id="save_as_template"><?php _e('Save as template'); ?></a> <?php } ?>
                                <a href="<?php echo get_the_permalink(); ?>" class="button button-secondary">Clear
                                    settings</a>
                            </div>

                            <br style="clear:both;">

                            <?php if (isset($row) && !empty($row)):
                                $str = <<< EOD
                        <div id="css-styling"><style type="text/css"></style></div>
                        <div class="row">
                            <div id="html-area" class="preview storeContent">
                                {$row['html']}
                            </div>
                        </div>       
EOD;
                                //add_action('wp_footer',array($this,'footer2'),11);
                                echo $str;

                                ?>

                            <?php else: ?>
                                <div id="css-styling">
                                    <style type="text/css"></style>
                                </div>


                                <div class="row">
                                    <div id="html-area" class="preview storeContent">
                                        <div class="opt-pricing-table opt-pricing-table-ul" id="opt-pricing-table">
                                            <ul>
                                                <li class="heading">&nbsp;</li>
                                                <li class="price">
                                                    <small>&nbsp;</small>
                                                    <br>Choose your plan
                                                </li>
                                                <li>Amount of space</li>
                                                <li>Bandwidth per month</li>
                                                <li>No. of email accounts</li>
                                                <li>No. of MySql Database</li>
                                                <li>24h support</li>
                                                <li>Supported Ticket per mo.</li>
                                                <li class="action">&nbsp;</li>
                                            </ul>

                                            <ul>
                                                <li class="heading">STANDARD</li>
                                                <li class="price"><strong>$10</strong><br>
                                                    <small>per month</small>
                                                </li>
                                                <li>10GB</li>
                                                <li>100GB</li>
                                                <li>1</li>
                                                <li>1</li>
                                                <li>No</li>
                                                <li>1</li>
                                                <li class="action"><a href="">sign up!</a></li>
                                            </ul>

                                            <ul class="feature">
                                                <li class="heading">PREMIUM</li>
                                                <li class="price"><strong>$30</strong><br>
                                                    <small>per month</small>
                                                </li>
                                                <li>30GB</li>
                                                <li>300GB</li>
                                                <li>5</li>
                                                <li>5</li>
                                                <li>Yes</li>
                                                <li>10</li>
                                                <li class="action"><a href="">sign up!</a></li>
                                            </ul>

                                            <ul>
                                                <li class="heading">VIP</li>
                                                <li class="price"><strong>$99</strong><br>
                                                    <small>per month</small>
                                                </li>
                                                <li>200GB</li>
                                                <li>800GB</li>
                                                <li>30</li>
                                                <li>30</li>
                                                <li>Yes</li>
                                                <li>30</li>
                                                <li class="action"><a href="">sign up!</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </form>
        </div>
        <script type="text/javascript">
            jQuery(function ($) {
                /*
                 $(".select2").select2({
                 placeholder: "Select a template",
                 allowClear: false
                 });

                 $('body').on('hover', '#opt-pricing-table ul', function () {
                 $(this).sortable({
                 items: "li:not(.heading, .price, .action)"
                 });
                 });

                 $('body').on('hover', '#opt-pricing-table', function () {
                 $(this).sortable({
                 items: "ul"
                 });
                 });

                 $('#showCtrlBtn').toggle(function () {
                 $('.settings-area').hide();
                 $(this).html('Show Controls');
                 $('.preview-area').css('width', '100%');
                 }, function () {
                 $('.settings-area').show();
                 $(this).html('Hide Controls');
                 $('.preview-area').css('width', '75%');
                 });
                 */
                $('body').on('click', '#add_new', function () {
                    var html = $.trim($('#html-area').html());
                    //alert(html);
                    var css = $.trim($('#css-styling style').html());
                    //alert(css);
                    //return;
                    var title = $.trim($('#post_title').val());
                    if (title === '') {
                        alert("Title is required.");
                        return;
                    }
                    var nonce = '<?php echo wp_create_nonce( "opt_pricing_tbl_add_new" ); ?>';
                    var edit = '<?php  echo $edit; ?>';
                    var formFields = $('#mainForm').serializeArray();

                    var style = '';
                    $.each(formFields, function (i, field) {
                        style += "'" + field.name + "'" + ":" +
                        "'" + field.value + "',\n";
                    });
                    //Dev mode
                    prompt("Copy to clipboard: Ctrl+C, Enter", style);
                    //$("#dev-mode").html(style);

                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        data: {
                            action: "opt_pricing_tbl_add_new",
                            title: title,
                            html: html,
                            css: css,
                            nonce: nonce,
                            edit: edit,
                            formFields: formFields
                        },
                        success: function (response) {
                            if (response.type == "success") {
                                window.location = '<?php echo add_query_arg(array('page' => 'optimal-pricing-tbl'),  admin_url('admin.php'));?>';
                            }
                            else {
                                console.log(response);
                                alert('Something went wrong');
                            }
                        }
                    });
                });

                $('body').on('click', '#save_as_template', function () {
                    var html = $.trim($('#html-area').html());
                    //alert(html);
                    var css = $.trim($('#css-styling style').html());
                    //alert(css);
                    //return;
                    var title = $.trim($('#post_title').val());
                    if (title === '') {
                        alert("Title is required.");
                        return;
                    }
                    var nonce = '<?php echo wp_create_nonce( "opt_pricing_tbl_save_template" ); ?>';
                    var edit = '<?php  echo $edit; ?>';
                    var formFields = $('#mainForm').serializeArray();

                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        data: {
                            action: "opt_pricing_tbl_save_as_template",
                            title: title,
                            html: html,
                            css: css,
                            nonce: nonce,
                            edit: edit,
                            formFields: formFields
                        },
                        success: function (response) {
                            if (response.type == "success") {
                                window.location = '<?php echo add_query_arg(array('page' => 'optimal-pricing-tbl'),  admin_url('admin.php'));?>';
                            }
                            else {
                                console.log(response);
                                alert('Something went wrong');
                            }
                        }
                    });
                });
            });
        </script>

    <?php
    }

    private function getPresetTemplates()
    {
        //die('herer');
        global $wpdb;
        $table = $wpdb->prefix . 'optimal_pricing_tbl_templates';
        $query = "SELECT * FROM $table";
        $result = $wpdb->get_results($query, ARRAY_A);
        if (!empty($result)) {
            $this->tempates = "<script type='text/javascript'>";
            foreach ($result as $row):
                $data = maybe_unserialize($row['extras']);
                $name = 'preset_' . $row['id'];
                $this->tempates .= "var $name = {";
                echo "<option value='$name'>{$row['title']}</option>";
                foreach ($data as $key => $value) {
                    $this->tempates .= "'$key' : '$value',";
                }
                $this->tempates .= "};";
                $this->tempates .= "presets.addPreset('$name',$name);";
            endforeach;
            $this->tempates .= "</script>";
        } else {
            echo "<option>No tempate found</option>";
        }
    }

    private function selectHtml($data, $other)
    {
        //print_r($other);
        $str = "";
        if (is_array($data)):
            $str .= "<select";
            if (isset($other['name']))
                $str .= " name='{$other['name']}' ";
            if (isset($other['id']))
                $str .= " id='{$other['id']}' ";
            if (isset($other['class']))
                $str .= " class='{$other['class']}' ";
            $str .= ">";
            if (isset($other['option']))
                $str .= $other['option'];
            foreach ($data as $key => $value):
                $sel = isset($other['selected']) && ($key == $other['selected']) ? 'selected="selected"' : "";
                $str .= "<option value='$key' $sel>$value</option>";
            endforeach;
            $str .= "</select>";
        endif;
        return $str;
    }

    private function getHeader()
    {
        ?>
        <div class="wrap">
        <div id="icon-edit" class="icon32 icon32-posts-post">&nbsp;</div>
        <h2><?php echo $this->title; ?></h2>
    <?php
    }

    private function getFooter()
    {
        echo '</div>';
    }
}

?>