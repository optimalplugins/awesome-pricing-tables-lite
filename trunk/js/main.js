function updateColourPickerValue(colourpicker)
{
//     var colourValue = colourpicker.toString();
//     $('#' + colourpicker.valueElement.id).attr('value', '#' + colourValue);
//     $('#' + colourpicker.valueElement.id).css('background-color', '#' + colourValue);
    styling.build();
}
var $ = jQuery.noConflict();

jQuery(document).ready(function( $ )
{
    $('#launch-tool').on('click', function()
    {
        window.location = $('.tools-select').val();
    });
});
/**
 * Forms Namespace
 */
var forms =
{
    init: function(callBack)
    {
        /**
         * Get select box values
         */
        $('.main-content select').on('change', function()
        {
            callBack();
        });

        $('input[type=text].slider').on('change', function(){
            callBack();
            $(this).prev().slider( 'value', $(this).val());
        });

        $('.main-content input[type=checkbox]').on('click', function(){
            callBack();
        });


    },
    initScrolling: function()
    {
        window.onscroll = function () { 
            var settingsArea = $('.settings-area').height();
            var scrollTop = document.body.scrollTop;
            if(scrollTop < settingsArea && $(window).width() > 768 && $(window).height() > 900)
            {
                $('.scrollHeight').css('padding-top', document.body.scrollTop);
            }
            else
            {
                $('.scrollHeight').css('padding-top', 0);
            }
        }; 

    },
    initSliders: function(callBack, onStop)
    {
        $('.slider').each(function(index)
        {
            var id = $(this).attr('id');
            var slideMin = $(this).attr('data-min');
            if (typeof slideMin == 'undefined' || slideMin == false) {
               slideMin = 0;
            }
            var slideMax = $(this).attr('data-max');
            if (typeof slideMax == 'undefined' || slideMax == false) {
                slideMax = 100;
            }
            var slideDefaultValue = $(this).attr('data-value');

            if(onStop == true)
            {
                $('#' + id + 'Slider').slider({
                min: parseInt(slideMin),
                max: parseInt(slideMax),
                value: slideDefaultValue,
                change: function( event, ui ) {
                    $( '#' + id).val(ui.value);
                    callBack();
                  }
                });
            }
            else
            {
                $('#' + id + 'Slider').slider({
                min: parseInt(slideMin),
                max: parseInt(slideMax),
                value: slideDefaultValue,
                slide: function( event, ui ) {
                    $( '#' + id).val(ui.value);
                    callBack();
                  }
                });
            }
        });
    },
    initTabs: function()
    {
        $('.nav-div').hide();
        $('.nav .active .nav-div').fadeIn(1000);

        // Nav click events
        $('.nav li h3').on('click', function(){
            $('.nav-div').hide();
            forms.showHtmlDiv( $(this) );
        });
    },
    showHtmlDiv: function(element)
    {

        var id = element.parent().attr('id');

        $('.preview-area').addClass('scrollHeight');
        var layout = 'sidebar';
        if(typeof settings != 'undefined')
        {
            layout = settings.layout;
        }

        switch(id)
        {
            
            case 'templates-tab':
            case 'community-tab':
            case 'instructions-tab':
            case 'moderate-tab':
            case 'html-tab':
            
                if(layout == 'sidebar' || id == 'html-tab')
                {
                    $('.preview-area').removeClass('scrollHeight');
                    $('.preview-area').css('padding-top',0);
                    if($('#'+id+'-div').css('display') == 'block')
                    {
                        return;
                    }

                    $('.preview-area').children().fadeOut(1000);
                    $('.preview-element-area').fadeOut(1000);
                    
                    $('#'+id+'-div').fadeIn(1000);

                    if(id == 'html-tab')
                    {
                        var html = $.trim($('#html-area').html());
                        $('.display-html-code code').html(generic.htmlEntities(html));

                        var css = $.trim($('#css-styling style').html());
                        $('.display-css-code code').html(generic.htmlEntities(css));

                        //Prism.highlightAll();
                    }
                    
                    setTimeout(function(){   
                        $('.preview-area').height($('#'+id+'-div').height());
                    }, 1000);

                }
                else
                {

                    $('.nav li').removeClass('active');
                    element.parent().addClass('active');
                    element.parent().find('.nav-div').fadeIn();

                    if($('.preview-element-area').css('display') == 'none')
                    {
                        $('.preview-area').children().fadeOut(1000);
                        $('.preview-element-area').fadeIn(1000);
                    }

                    $('.preview-area').css('height', 'auto');
                }
   
            break;
            default:
                $('.nav li').removeClass('active');
                element.parent().addClass('active');
                element.parent().find('.nav-div').fadeIn();

                if($('.preview-element-area').css('display') == 'none')
                {
                    $('.preview-area').children().fadeOut(1000);
                    $('.preview-element-area').fadeIn(1000);
                }

                $('.preview-area').css('height', 'auto');
            break;
        }


        if(id == 'html-tab')
        {
            $("html, body").animate({ scrollTop: 0 }, "slow");
        }
        else if(id != '' && $('#' + id).offset() != undefined)
        {
            $('html, body').stop().animate({
                'scrollTop': $('#' + id).offset().top - 100
            }, 1000, 'swing');
        }
        
    },
    initColourPicker: function(callBack)
    {
        if(callBack != null)
        {
            var options = {
                hide: false,
                change: function(event, ui){
                    callBack()
                }
            };
        }
        else
        {
            var options = {hide: false};
        }
        $('.colour-picker').iris(options);
    },
    initPresets: function()
    {
        if($('#storePresetForm #storePreset').length > 0 && $('#storePresetForm #storePreset').val() != '')
        {
            presets.addPreset('postpreset', JSON.parse($('#storePresetForm #storePreset').val()));

            if($('#storePresetForm #storeContent').length > 0 && $('#storePresetForm #storeContent').val() != null)
            {
                presets.setContent('postpreset', $('#storePresetForm #storeContent').val());
            }

            presets.loadPreset('default');
            presets.loadPreset('postpreset', true);
        }

        $('#storePresetForm').on('submit', function()
        {
            if (typeof store_preset_pre_build_hook == 'function')
            {
               $('#storePresetForm #storeJs').val(store_preset_pre_build_hook(styling));
            }

            if (typeof store_preset_post_build_hook == 'function')
            {
               $('#storePresetForm #storeJs').val(store_preset_post_build_hook(styling));
            }

            $('#storePresetForm #storePreset').val(JSON.stringify(styling.preset_values));

            // Hook for processing what and how you want to store javascript (place in tool js file)
            if (typeof store_js_hook == 'function')
            {
               $('#storePresetForm #storeJs').val(store_js_hook());
            }


            if (typeof store_content_hook == 'function')
            {
               $('#storePresetForm #storeContent').val(store_content_hook());
            }
            //console.log($('#storePresetForm #storeContent').val());
            //console.log($('#storePresetForm #storeJs').val());
            styling.build();

            return true;
        });

        $('.mod-preset-view').on('click', function(){
            var presetCode = $(this).parent().next().val();

            $('#presetId').val($(this).parent().parent().find('#mod-preset-id').val());
            $('#presetName').val($(this).parent().parent().find('#mod-preset-name').val());
            $('#share_url').val('');

            $('#storeExistingPresetSubmit').remove()

            presets.addPreset('moderate-preset', JSON.parse(presetCode));
            presets.loadPreset('default');
            presets.loadPreset('moderate-preset', true);
        });
    }
}

/**
 * Generic name for helper functions
 */
var generic =
{
    htmlEntities: function (str)
    {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    },
    objectSize: function(obj)
    {
        var size = 0, key;
        for (key in obj)
        {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    },
    getId: function(element)
    {
        return $(element).attr('id').split('_')[1];
    }
}

/**
 * Presets namespace for pre-defined
 * @type {[type]}
 */

var newCtrl = {
  init: function() {
      $('.new_presets').on('click',function(){
          var preset = $(this).attr('id');
          presets.loadPreset('default');
          presets.loadPreset(preset, true);
      });
  }  
};

var presets =
{
    list: new Object(),
    defaults: new Object(),
    content: new Object(),
    init: function()
    {
        $('.presets,.mypresets').on('change', function()
        {
            //console.log($('#css-styling').html());
            //$('#css-styling').html('');
            //console.log($('#css-styling').html());
            var preset = $(this).val();
            var component = $('ul.nav').find('input,select,checkbox');
            component.each(function(){
                if($(this).attr('name') === 'preset' || $(this).attr('name') === 'preset2') {
                    
                }
                else {
                    $(this).val('');
                }
            });
            presets.loadPreset('default');
            presets.loadPreset(preset, true);
            
            if(typeof settings != 'undefined' && settings.layout == 'wide')
            {
                $('html, body').stop().animate({
                    'scrollTop': $('.preview-area').offset().top - 100
                }, 1000, 'swing');
                $('.nav li').removeClass('active');
                $('#templates-tab-div').hide();
                $('.first-setting h3').parent().addClass('active');
                $('.first-setting h3').parent().find('.nav-div').fadeIn();

            }
            else
            {
                forms.showHtmlDiv($('.first-setting h3'));
            }
        });
    },
    loadPreset: function(presetName, runHooks)
    {
        if(runHooks==true)
        {
            // Load content hook - provides content from form
            if (typeof load_preset_content_hook == 'function')
            {
                load_preset_content_hook($('#storePresetForm #storeContent').val());
            }

            // Load js hook - provides content from form
            if (typeof load_preset_js_hook == 'function' && $('#storePresetForm #storeJs').val() != '')
            {
                load_preset_js_hook($('#storePresetForm #storeJs').val());
            }
        }
        var preset = this.list[presetName];

        if(preset == undefined)
        {
            return false;
        }

        $.each(preset, function(index, value)
        {
            if(index.indexOf('_') != 0)
            {
                if(value == 'transparent')
                {
                    value = '';
                }

                if($('#' + index).attr('type') == 'checkbox' && value == 1)
                {
                    $('#' + index).attr('checked','checked');
                }
                else if($('#' + index).attr('type') == 'checkbox' && value == 0)
                {
                    $('#' + index).removeAttr('checked');
                }
                else
                {
                    $('#' + index).val(value);
                }

                if($('#' + index + 'Slider').length > 0)
                {
                    $( '#' + index + 'Slider').slider( "value", value );
                }

                switch(index)
                {
                    case 'backgroundColourType':
                    case 'backgroundColourTypeHover':
                    case 'backgroundColourTypeActive':
                    case 'backgroundColourType':
                    case 'addLeftShadowElement':
                    case 'addRightShadowElement':
                        $('#' + index).trigger('change');
                    break;
                }
            }
        });

        var content = this.content[presetName];
        if(content != null)
        {
            $('.storeContent').html(content);
        }

        styling.build();

        // Update any of the color pickers
        $('.color').each(function()
        {
            if($(this)[0].color != undefined)
            {
                $(this)[0].color.fromString($(this).val());
            }
        });
    },
    addPreset: function(name, values)
    {
        this.list[name] = values;
    },
    setContent: function(name, value)
    {
        this.content[name] = value;
    },
    setDefault: function(preset)
    {
        this.list['default'] = preset;
    }
}

/**
 * Styling namespace for css and html outputs
 */
var styling =
{
    classes: new Array(),
    styling_object: new Object(),
    preset_values: new Object(),

    add_class: function(className, classObj)
    {
        this.classes[className] = classObj;
    },
    remove_class: function(className)
    {
        this.classes[className] = new Object();
    },
    build: function()
    {

        // Need to clear the styling_object each time re-build
        styling.preset_values = new Object(),
        styling.styling_object = new Object();
        styling.create_styling_object();
        styling.render_styling();
    },
    render_styling: function()
    {
        // Load content hook - provides content from form
        if (typeof pre_render_hook == 'function')
        {
            this.styling_object = pre_render_hook(this.styling_object);
        }

        var styleText = '';
        for (var key in this.styling_object)
        {
            var size = generic.objectSize(this.styling_object[key]);
            if(size > 0)
            {
                styleText += key + "{ \n";
                    $.each( this.styling_object[key], function( property, value )
                    {
                        if(property == 'text')
                        {
                            return true;
                        }

                        if(value != '' && value != null)
                        {
                            if(typeof value === 'object')
                            {
                                $.each( value, function(index, cssValue){
                                    if(cssValue != '')
                                    {
                                        styleText += '    ' + property + ': ' + cssValue + " !important; \n";
                                    }
                                });
                            } else {
                                styleText += '    ' + property + ': ' + value + " !important; \n";
                            }
                        }
                    });
                styleText += "} \n";
            }
        }

        var defaultStyling = '';
        if(typeof settings !== 'undefined' && settings.defaultStyling != '')
        {
            defaultStyling = settings.defaultStyling;
        }
        //console.log(defaultStyling);
        console.log(styleText);
        $('#css-styling').html('<style type="text/css">' + defaultStyling + ' ' + styleText + '</style>');
    },
    create_styling_object: function()
    {
        for (var key in styling.classes)
        {
            var groups = new Object();

            $.each( styling.classes[key], function( id, cssproperty )
            {
                if(id.indexOf('_') == 0)
                {
                    var val = id.substring(1);
                }
                else
                {
                    // Check the element exists
                    if($('#' + id).length == 0)
                    {
                        return true;
                    }
                    
                    var val = $('#' + id).val();
                    if($('#' + id).attr('type') == 'checkbox')
                    {
                        //var val = id.substring(1);
                        if($('#' + id).attr('type') == 'checkbox')
                        {
                            if($('#' + id).is(':checked'))
                            {
                                val = 1;
                            }
                            else
                            {
                                val = 0;
                            }
                        }
                    }
                    else
                    {
                        var val = $('#' + id).val();
                        if($('#' + id).attr('type') == 'checkbox')
                        {
                            if($('#' + id).is(':checked'))
                            {
                                val = 1;
                            }
                            else
                            {
                                val = 0;
                            }
                        }
                    }
                }
                

                styling.add_preset_item(id, val);

                if(!$.isArray(cssproperty))
                {
                    if(cssproperty != '')
                    {
                        if(key.charAt(0) == '*')
                        {
                            styling.add_styling_item(key, cssproperty, val, id);
                        } else {
                            styling.add_styling_item('.' + key, cssproperty, val, id);
                        }
                    }
                }
                else
                {
                    // 0 - CSS property
                    // 1 - Group
                    // 2 - Change value
                    // 3 - suffix

                    if(!groups.hasOwnProperty(cssproperty[1]))
                    {
                        groups[cssproperty[1]] = new Object();
                    }

                    if(!groups[cssproperty[1]].hasOwnProperty(cssproperty[0]))
                    {
                        groups[cssproperty[1]][cssproperty[0]] = new Object();
                    }

                    var length = 0;

                    if(groups[cssproperty[1]][cssproperty[0]] != undefined)
                    {
                        length = Object.keys(groups[cssproperty[1]][cssproperty[0]]).length;
                    }

                    // suffix
                    if(cssproperty[2] != undefined && val != '')
                    {
                        if(cssproperty[0] == 'filter')
                        {
                            val = cssproperty[2] + '(' + val;
                        }

                        switch(cssproperty[2])
                        {
                            case 'px':
                            case '%':
                            case '':
                            case 'deg':
                                val = val + cssproperty[2];
                            break;

                            case 'inset':
                                if(val == 1)
                                {
                                    val = 'inset';
                                } else {
                                    return true;
                                }
                            break;

                            case 'rotate':
                                val = 'rotate(' + val + 'deg)';
                            break;

                            case 'skew':
                                val = 'skew(' + val + 'deg)';
                            break;

                            case 'grayscale':
                            case 'sepia':
                            case 'invert':
                            case 'opacity':
                            case 'brightness':
                            case 'contrast':
                                val = val + '%';
                                break;

                            case 'blur':
                                val = val + 'px';
                                break;

                            case 'saturate':
                                val = val;
                                break;

                            case 'hue-rotate':
                                val = val + 'deg';
                                break;
                        }

                        if(cssproperty[0] == 'filter')
                        {
                            val = val + ')';
                        }
                    }

                    // Transparent value change
                    if(cssproperty[2] == 'transparent' && val == '' && length > 0)
                    {
                        val = 'transparent';
                    }

                    if(val != '')
                    {
                        if(cssproperty[3] != undefined)
                        {
                            val = val + cssproperty[3];
                        }

                        groups[cssproperty[1]][cssproperty[0]][length] = val;
                    }
                }
            });

            $.each( groups , function(groupName, propertyValues)
            {
                val = '';

                $.each(propertyValues, function(cssName, cssValues){
                    cssproperty = cssName;
                    val = styling.objToString(cssValues);
                });

                val = val.replace(/,+$/, "");

                if(val != '')
                {
                    styling.add_styling_item('.' + key, cssproperty, val);
                }
            });
        }
    },

    objToString: function (obj) {
        var str = '';
        for (var p in obj) {
            if (obj.hasOwnProperty(p)) {
                if(str != '')
                {
                    str += ' ';
                }

                str += obj[p];
            }
        }
        return str;
    },
    add_preset_item: function(id, val)
    {
        if(this.preset_values[id] == undefined)
        {
            this.preset_values[id] = new Object();
        }

        this.preset_values[id] = val;
    },
    add_styling_item: function(selector, cssproperty, value, id)
    {
        if(this.styling_object[selector] == undefined)
        {
            this.styling_object[selector] = new Object();
        }

        if(value != '' || parseInt(value) == 0)
        {
            switch(cssproperty)
            {
                case 'background-gradient':

                    var from = $('#' + id.substr(0, id.length - 1) + 1 );

                    this.styling_object[selector]['background-color'] = '';

                    this.styling_object[selector]['background'] = new Object();
                    this.styling_object[selector]['background'][0] = from.val();
                    this.styling_object[selector]['background'][1] = '-moz-linear-gradient(top, ' + from.val() + ' 0%, ' + value + ' 100%)';
                    this.styling_object[selector]['background'][2] = '-webkit-gradient(linear, left top, left bottom, color-stop(0%,' + from.val() + '), color-stop(100%,' + value + '))';
                    this.styling_object[selector]['background'][3] = '-webkit-linear-gradient(top, ' + from.val() + ' 0%,' + value + ' 100%)';
                    this.styling_object[selector]['background'][4] = '-o-linear-gradient(top, ' + from.val() + ' 0%,' + value + ' 100%)';
                    this.styling_object[selector]['background'][5] = '-ms-linear-gradient(top, ' + from.val() + ' 0%,' + value + ' 100%)';
                    this.styling_object[selector]['background'][6] = 'linear-gradient(to bottom, ' + from.val() + ' 0%,' + value + ' 100%)';
                    this.styling_object[selector]['filter'] = 'progid:DXImageTransform.Microsoft.gradient( startColorstr=\'' + from.val() + '\',  endColorstr=\'' + value + '\', GradientType=0 )';
                break;

                case 'font-family':
                    var fontStylesheet = $('#google-font-selector');
                    if(fontStylesheet.length)
                    {
                        fontStylesheet.remove();
                    }

                    var fontPrefix = '';
                    if(value != undefined && value != 'Arial' && value != 'Courier New' && value != 'Georgia' && value != 'Impact' && value != 'Times New Roman' && value != 'Trebuchet MS' && value != 'Verdana')
                    {
                        var useSelector = selector;
                        if(selector.indexOf(' ') >= 0)
                        {
                            useSelector = selector.split(',')[0];
                        }


                        $(useSelector).parent().append('\r<link href="http://fonts.googleapis.com/css?family=' + value.replace(' ', '+') + '" id="google-font-selector" rel="stylesheet" type="text/css">');
                        fontPrefix = ', sans-serif;'

                        //console.log($(useSelector).parent().html());
                    }

                    this.styling_object[selector][cssproperty] = "'" + value + "'" + fontPrefix;
                break;

                case 'font-weight':
                    if(value == 1 && id == 'fontBold')
                    {
                        this.styling_object[selector][cssproperty] = 'bold';
                    } else {
                        // this.styling_object[selector][cssproperty] = 'normal';
                    }
                break;

                case 'font-style':
                    if(value == 1 && id == 'fontItalic')
                    {
                        this.styling_object[selector][cssproperty] = 'italic';
                    } else {
                        // this.styling_object[selector][cssproperty] = 'normal';
                    }
                break;

                case 'text-decoration':
                    if(value == 1 && id == 'fontUnderline')
                    {
                        this.styling_object[selector][cssproperty] = 'underline';
                    } else {
                        // this.styling_object[selector][cssproperty] = 'none';
                    }
                break;

                case 'box-sizing':
                    if(value == 1)
                    {
                        this.styling_object[selector][cssproperty] = 'border-box';
                        this.styling_object[selector]['-moz-' + cssproperty] = 'border-box';
                        this.styling_object[selector]['-webkit-' + cssproperty] = 'border-box';
                    }
                break;

                case 'line-height':
                case 'height':
                case 'width':
                case 'padding':
                case 'font-size':
                case 'letter-spacing':
                case 'border-width':
                case 'border-radius':
                case 'border-bottom-width':
                case 'margin':
                case 'top':
                case 'right':
                case 'bottom':
                case 'left':
                case 'padding-bottom':
                case 'margin-bottom':
                case 'padding-top':
                case 'padding-left':
                case 'padding-right':
                case 'border-top-left-radius':
                case 'border-top-right-radius':
                case 'border-bottom-left-radius':
                case 'border-bottom-right-radius':
                    if(value == undefined)
                    {
                        value = '';
                    }
                    if(value.indexOf('%') == '-1')
                    {
                        this.styling_object[selector][cssproperty] = value + 'px';
                    } else {
                        this.styling_object[selector][cssproperty] = value;
                    }

                    if(cssproperty == 'height' && $('#' + id).attr('data-lineheight') == '1')
                    {
                        this.styling_object[selector]['line-height'] = value + 'px';
                    }

                break;
                case 'max-width':
                case 'min-width':
                    this.styling_object[selector][cssproperty] = value + 'px';
                break;
                case 'min-width-percent':
                    this.styling_object[selector]['min-width'] = value + '%';
                break;
                case 'width-percent':
                    this.styling_object[selector]['width'] = value + '%';
                break;
                case 'margin-percent':
                    this.styling_object[selector]['margin'] = value + '%';
                break;
                case 'feature-effect':
                    this.styling_object[selector]['-webkit-transform'] = 'scale(1.1)';
                    this.styling_object[selector]['transform'] = 'scale(1.1)';
                    this.styling_object[selector]['box-shadow'] = '3px 5px 7px rgba(0,0,0,.7)';
                break;


                case 'padding-top-bottom':
                        this.styling_object[selector]['padding-top'] = value + 'px';
                        this.styling_object[selector]['padding-bottom'] = value + 'px';
                    break;

                case 'padding-left-right':
                        this.styling_object[selector]['padding-left'] = value + 'px';
                        this.styling_object[selector]['padding-right'] = value + 'px';
                    break;

                case 'hover-effect':
                    if(value == '1')
                    {
                        if(!this.styling_object.hasOwnProperty(selector + ':hover'))
                        {
                            this.styling_object[selector + ':hover'] = new Object();
                        }
                        this.styling_object[selector]['-webkit-transform'] = 'scale(1.1)';
                        this.styling_object[selector]['transform'] = 'scale(1.1)';
                        this.styling_object[selector]['box-shadow'] = '3px 5px 7px rgba(0,0,0,.7)';
                    }
                break;

                case 'show-hide':
                    if(value == '1')
                    {
                        this.styling_object[selector]['display'] = 'inline';
                    } else {
                        this.styling_object[selector]['display'] = 'none';
                    }
                    break;

                case 'button-shadow-effect':
                    if(value == '1')
                    {
                        if(!this.styling_object.hasOwnProperty(selector + ':hover'))
                        {
                            this.styling_object[selector + ':hover'] = new Object();
                        }
                        this.styling_object[selector]['text-shadow'] = '0px 1px 0px #ccc';
                        this.styling_object[selector]['box-shadow'] = '0px 1px 0px rgba(255,255,255,0.5)';
                        this.styling_object[selector]['-webkit-box-shadow'] = '0px 1px 0px rgba(255,255,255,0.5)';
                        this.styling_object[selector]['-moz-box-shadow'] = '0px 1px 0px rgba(255,255,255,0.5)';
                    }
                    break;

                case 'remove-border-bottom-checkbox':
                    if(value == '1')
                    {
                        this.styling_object[selector]['border-bottom'] = '0';
                    }
                break;

                case 'transform':
                case 'filter':
                    this.styling_object[selector]['-webkit-' + cssproperty] = value;
                    this.styling_object[selector][cssproperty] = value;
                break;

                default:
                    this.styling_object[selector][cssproperty] = value;
                break;
            }
        } else {
            if(cssproperty == 'content')
            {
                this.styling_object[selector][cssproperty] = '""';
            }
        }
    }
}