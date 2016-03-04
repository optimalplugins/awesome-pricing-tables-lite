var $ = jQuery.noConflict();
jQuery(document).ready(function ($) {
    pricingTables.init();
});

var settings =
{
    defaultStyling: '',
    layout: 'side'
}

var previousTarget = null;

var pricingTables =
{
    init: function () {
        this.initClasses();
        //newCtrl.init();
        presets.init();
        this.initForm();
        this.initPreview();
        this.initAddOptions();
        this.initConfigs();
        this.initPresets();
        this.initControls();
        this.preventLinks();
        styling.build();

        this.resetControls();
        $('#layout-tab').show();

    },
    preventLinks: function () {
        // Disable accidental click of action button
        $('#opt-pricing-table').delegate('.action a', 'click', function (e) {
            e.preventDefault();
        });
    },
    initControls: function () {
        $('#removeColumn').on('change', function () {
            var columnRemove = $(this).val();
            if (columnRemove != '') {
                $('#opt-pricing-table ul:nth-child(' + columnRemove + ')').remove();
                pricingTables.resetControls();
            }
        });
        $('#removeRow').on('change', function () {
            var rowRemove = $(this).val();
            if (rowRemove != '') {
                var totalColumns = $('#opt-pricing-table ul').length;
                for (i = 1; i <= totalColumns; i++) {
                    $('#opt-pricing-table ul:nth-child(' + i + ') li:nth-child(' + rowRemove + ')').remove();
                }

                pricingTables.resetControls();
            }
        });
    },
    resetControls: function () {
        // Reset remove column options
        $('#removeColumn')
            .find('option')
            .remove()
            .end()
            .append('<option>Remove Column</option>');

        var totalColumns = $('#opt-pricing-table ul').length;
        if (totalColumns != 1) {
            for (i = 1; i <= totalColumns; i++) {
                $('#removeColumn')
                    .append($("<option></option>")
                        .attr("value", i)
                        .text('Remove Col ' + i));
            }
        }

        // Reset remove row options
        $('#removeRow')
            .find('option')
            .remove()
            .end()
            .append('<option>Remove Row</option>');

        var totalRows = $('#opt-pricing-table ul:nth-child(1) li').length;
        if (totalRows != 2) {
            for (i = 2; i < totalRows; i++) {
                $('#removeRow')
                    .append($("<option></option>")
                        .attr("value", i)
                        .text('Remove Row ' + i));
            }
        }
    },
    setPriceRow: function () {
        $('#opt-pricing-table li').removeClass('price');
        $('*[class=""]').removeAttr('class');
        var optionRow = $('#priceOptions').val();
        var totalColumns = $('#opt-pricing-table ul').length;
        for (i = 1; i <= totalColumns; i++) {
            $('#opt-pricing-table ul:nth-child(' + i + ') li:nth-child(' + optionRow + ')')
                .addClass('price');
        }
    },
    initConfigs: function () {
        $('#priceOptions').change(function () {
            pricingTables.setPriceRow();
        });
        $('#featureColumn').change(function () {
            $('#opt-pricing-table ul').removeClass('feature');
            $('*[class=""]').removeAttr('class');
            var featureColumn = $('#featureColumn').val();
            if (featureColumn != '') {
                $('#opt-pricing-table ul:nth-child(' + featureColumn + ')')
                    .addClass('feature');
            }

        });
    },

    initAddOptions: function () {
        $('.addColumn').click(function () {
            var totalItems = $("#opt-pricing-table ul:first-child li").size();
            var newList = "<ul>";

            for (i = 1; i <= totalItems; i++) {
                if (i == 1) {
                    newList += "<li class='heading'> - </li>";
                }
                else if (i == 2) {
                    newList += "<li class='price'> - </li>";
                }
                else {
                    if (i == totalItems) {
                        newList += '<li class="action"> - </li>';
                    }
                    else {
                        newList += "<li> - </li>";
                    }
                }
            }

            newList += "</ul>";

            if ($('#opt-pricing-table link:last-child').length != 0) {
                $('#opt-pricing-table link:last-child').before(newList);
            }
            else {
                $('#opt-pricing-table').append(newList);
            }

            var totalColumns = $("#opt-pricing-table ul").size();

            $('#featureColumn').append('<option value="' + totalColumns + '">Column ' + totalColumns + '</option>');

            pricingTables.setPriceRow();
            pricingTables.resetControls();
        });

        $('.addRow').click(function () {
            var totalItems = $("#opt-pricing-table ul:first-child li").size();
            var totalColumns = $('#opt-pricing-table ul').length;
            var listItem = "<li> - </li>";

            for (i = 1; i <= totalColumns; i++) {
                $('#opt-pricing-table ul:nth-child(' + i + ') li:nth-child(' + (totalItems - 1) + ')')
                    .after(listItem);
            }

            $('#priceOptions').append('<option value="' + totalItems + '">Row ' + totalItems + '</option>');
            pricingTables.resetControls();
        });

        /*
         $('#actionButton').click(function()
         {
         if(!$(this).is(':checked'))
         {
         var totalColumns = $('#opt-pricing-table ul').length;
         for(i=1;i<=totalColumns;i++)
         {
         var myText = $('#opt-pricing-table ul:nth-child(' + i + ') li:last-child a').text();
         $('#opt-pricing-table ul:nth-child(' + i + ') li:last-child').html(myText);
         }
         }
         else
         {
         var totalColumns = $('#opt-pricing-table ul').length;
         for(i=1;i<=totalColumns;i++)
         {
         var preContent = $('#opt-pricing-table ul:nth-child(' + i + ') li:last-child').html();
         var newContent = '<a href="">' + preContent + '</a>';
         $('#opt-pricing-table ul:nth-child(' + i + ') li:last-child').html(newContent);
         }
         }
         });
         */
    },

    initClasses: function () {
        var pricingTable =
        {

        }

        styling.add_class('opt-pricing-table ', pricingTable);

        var pricingTableCol =
        {
            'lineHeight': 'line-height',
            'borderWidth': 'border-width',
            'borderStyle': 'border-style',
            'borderColour': 'border-color',
            'borderRadius': 'border-radius',
            'margin': 'margin',
            'columnWidth': 'width',
            'textAlign': 'text-align',
            'fontFamily': 'font-family',
            'list-style': 'list-style',
            'float': 'float',
            'padding': 'padding',
            'columnBackgroundColour': 'background-color'
        }

        styling.add_class('opt-pricing-table ul', pricingTableCol);

        var pricingTableTitleColumn =
        {
            'titleColumnShowHideCheckbox': 'show-hide'
        }
        styling.add_class('opt-pricing-table > ul:first-child', pricingTableTitleColumn);

        var pricingTableHover =
        {
            'hoverEffectCheckbox': 'hover-effect'
        }
        styling.add_class('opt-pricing-table ul:hover', pricingTableHover);

        var items =
        {
            'cellHeight': 'height',
            'cellPaddingTop': 'padding-top',
            'rowBackgroundColour': 'background-color',
            'cellBorderWidth': 'border-width',
            'cellBorderStyle': 'border-style',
            'cellBorderColour': 'border-color',
            'cellBorderRadius': 'border-radius',
            'cellBottomBorderWidth': 'border-bottom-width',
            'rowFontSize': 'font-size',
            'itemTextColour' : 'color'
        }

        styling.add_class('opt-pricing-table ul li', items);

        var topCellBorder =
        {
            'removeTopCellBorderBottom': 'remove-border-bottom-checkbox'
        }

        styling.add_class('opt-pricing-table li:first-child', topCellBorder);

        var bottomCellBorder =
        {
            'removeBottomCellBorderBottom': 'remove-border-bottom-checkbox'
        }
        styling.add_class('opt-pricing-table li:last-child', bottomCellBorder);

        var oddItems =
        {
            'alternateRowBackgroundColour': 'background-color'
        }

        styling.add_class('opt-pricing-table li:nth-child(odd)', oddItems);

        var heading =
        {
            'headingTextColour': 'color',
            'backgroundColour1': 'background-color',
            'backgroundColour2': 'background-gradient',
            'headingFontSize': 'font-size',
            'headingCellHeight': 'height',
            'headingCellPaddingTop': 'padding-top'
        }

        styling.add_class('opt-pricing-table ul .heading', heading);

        var pricingRow =
        {
            'pricingTextColour': 'color',
            'pricingBackgroundColour1': 'background-color',
            'pricingBackgroundColour2': 'background-gradient',
            'pricingFontSize': 'font-size',
            'pricingCellHeight': 'height',
            'pricingCellPaddingTop': 'padding-top'
        }

        styling.add_class('opt-pricing-table ul .price', pricingRow);

        var feature =
        {
            'featureColumn': 'feature-effect'
        }

        styling.add_class('feature', feature);

        var action =
        {
            'callToActionBackgroundColour1': 'background-color',
            'callToActionBackgroundColour2': 'background-gradient',
            'callToActionCellHeight': 'height',
            'callToActionCellPaddingTop': 'padding-top'
        }

        styling.add_class('opt-pricing-table ul .action', action);

        var button =
        {
            'buttonFontSize': 'font-size',
            'buttonTextColour': 'color',
            'buttonBorderColour': 'border-color',
            'buttonBorderWidth': 'border-width',
            'buttonBorderRadius': 'border-radius',
            'buttonBackgroundColour1': 'background-color',
            'buttonBackgroundColour2': 'background-gradient',
            'buttonBorderPaddingTopBottom': 'padding-top-bottom',
            'buttonBorderPaddingLeftRight': 'padding-left-right',
            'buttonBorderStyle': 'border-style',
            'buttonShadowEffect': 'button-shadow-effect'
        }

        styling.add_class('opt-pricing-table .action a', button);

        /*
         var feature =
         {

         'featureBorderWidth' : 'border-width',
         'featureBorderStyle' : 'border-style',
         'featureBorderColour' : 'border-color',
         'featureHeadingTextColour' : 'color',
         'featureHeadingBackgroundColour' : 'background-color',
         'featurePricingTextColour' : 'color',
         'featurePricingBackgroundColour' : 'background-color',
         'featureRowBackgroundColour' : 'background-color',
         'featureAlternateRowBackgroundColour' : 'background-color-odd',
         'featureHeadingFontSize' : 'font-size',
         'featureRowFontSize' : 'font-size',
         'featurePricingFontSize' : 'font-size'
         }

         styling.add_class('opt-pricing-table ul .price', feature);*/

    },
    initForm: function () {
        $('.colour-picker').change(function () {
            styling.build();
        });

        forms.initTabs();
        forms.initSliders(styling.build);
        forms.init(styling.build);
        forms.initPresets();
    },
    initPreview: function () {
        // move from addNewTable.php - START
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
        // move from addNewTable.php -END

        // Allow pricing table options to be editable
        $('#opt-pricing-table').delegate("li", "click", function () {

            if ($('#activeEditing').length == 0) {

                var currentText = $(this).html().replace(/"/g, "'");
                $(this).html('<input type="text" id="activeEditing" value="' + currentText + '" />');

                $('#activeEditing').focus();
                $('#activeEditing').bind("blur", function (e) {
                    var newText = $('#activeEditing').val();
                    if (newText == '') {
                        newText = '&nbsp;';
                    }
                    $('#activeEditing').parent().html(newText, function () {
                        pricingTables.preventLinks();
                    });
                });
            } else {
                if ($(this).html().indexOf('activeEditing') != -1 ) {
                    return;
                }

                $('#activeEditing').blur();
                var currentText = $(this).html().replace(/"/g, "'");
                $(this).html('<input type="text" id="activeEditing" value="' + currentText + '" />');
                $('#activeEditing').focus();
                $('#activeEditing').bind("blur", function (e) {
                    var newText = $('#activeEditing').val();
                    if (newText == '') {
                        newText = '&nbsp;';
                    }
                    $('#activeEditing').parent().html(newText, function () {
                        pricingTables.preventLinks();
                    });
                });
            }
        });
    },
    initPresets: function () {
        var preset1 =
        {
            'margin':'0',
            'padding':'0',
            'hoverEffectCheckbox':'yes',
            'priceOptions':'2',
            'featureColumn':'',
            'headingTextColour':'#F4FEFF',
            'backgroundColour1':'#CF1018',
            'backgroundColour2':'#CF1018',
            'pricingTextColour':'#742300',
            'pricingBackgroundColour1':'#FCCE3A',
            'pricingBackgroundColour2':'#FCCE3A',
            'itemTextColour':'#600000',
            'rowBackgroundColour':'#E3E3E3',
            'alternateRowBackgroundColour':'#F6F6F6',
            'columnBackgroundColour':'#FFFFFF',
            'callToActionBackgroundColour1':'#CF1018',
            'callToActionBackgroundColour2':'#CF1018',
            'buttonTextColour':'#600000',
            'buttonBackgroundColour1':'#FCCE3A',
            'buttonBackgroundColour2':'#FCCE3A',
            'buttonBorderColour':'#CC0000',
            'fontFamily':'Open Sans',
            'textAlign':'center',
            'lineHeight':'21',
            'headingFontSize':'25',
            'pricingFontSize':'21',
            'rowFontSize':'16',
            'buttonFontSize':'',
            'headingCellHeight':'80',
            'headingCellPaddingTop':'34',
            'pricingCellHeight':'91',
            'pricingCellPaddingTop':'23',
            'cellHeight':'46',
            'cellPaddingTop':'13',
            'columnWidth':'200',
            'callToActionCellHeight':'65',
            'callToActionCellPaddingTop':'21',
            'buttonBorderPaddingTopBottom':'5',
            'buttonBorderPaddingLeftRight':'20',
            'buttonBorderStyle':'',
            'buttonBorderRadius':'5',
            'buttonBorderWidth':'',
            'borderColour':'#FFFFFF',
            'borderStyle':'solid',
            'borderWidth':'0',
            'borderRadius':'3',
            'cellBorderColour':'#B83737',
            'cellBorderStyle':'',
            'cellBorderWidth':'2',
            'cellBottomBorderWidth':'2',
            'cellBorderRadius':'0',
            'extra_css':''
        }
        presets.addPreset('preset1', preset1);

        var preset2 =
        {
            'margin':'0',
            'padding':'0',
            'hoverEffectCheckbox':'',
            'priceOptions':'2',
            'featureColumn':'',
            'headingTextColour':'#F8F8F8',
            'backgroundColour1':'#0C203B',
            'backgroundColour2':'#0C203B',
            'pricingTextColour':'#F8F8F8',
            'pricingBackgroundColour1':'#B30B1A',
            'pricingBackgroundColour2':'#B30B1A',
            'itemTextColour':'#002D5C',
            'rowBackgroundColour':'#73A1C5',
            'alternateRowBackgroundColour':'#FFFFFF',
            'columnBackgroundColour':'#FFFFFF',
            'callToActionBackgroundColour1':'#0C203B',
            'callToActionBackgroundColour2':'#0C203B',
            'buttonTextColour':'#F8F8F8',
            'buttonBackgroundColour1':'#B30B1A',
            'buttonBackgroundColour2':'#B30B1A',
            'buttonBorderColour':'#B30B1A',
            'fontFamily':'Open Sans',
            'textAlign':'center',
            'lineHeight':'21',
            'headingFontSize':'25',
            'pricingFontSize':'21',
            'rowFontSize':'16',
            'buttonFontSize':'',
            'headingCellHeight':'80',
            'headingCellPaddingTop':'34',
            'pricingCellHeight':'91',
            'pricingCellPaddingTop':'23',
            'cellHeight':'46',
            'cellPaddingTop':'13',
            'columnWidth':'200',
            'callToActionCellHeight':'65',
            'callToActionCellPaddingTop':'21',
            'buttonBorderPaddingTopBottom':'5',
            'buttonBorderPaddingLeftRight':'20',
            'buttonBorderStyle':'',
            'buttonBorderRadius':'5',
            'buttonBorderWidth':'0',
            'borderColour':'#FFFFFF',
            'borderStyle':'solid',
            'borderWidth':'0',
            'borderRadius':'3',
            'cellBorderColour':'#B83737',
            'cellBorderStyle':'',
            'cellBorderWidth':'2',
            'cellBottomBorderWidth':'2',
            'cellBorderRadius':'0',
            'extra_css':''
        }
        presets.addPreset('preset2', preset2);

        var preset3 =
        {
            'margin':'0',
            'padding':'0',
            'priceOptions':'2',
            'featureColumn':'',
            'headingTextColour':'#8AC453',
            'backgroundColour1':'#605074',
            'backgroundColour2':'#605074',
            'pricingTextColour':'#FFFFFF',
            'pricingBackgroundColour1':'#18603B',
            'pricingBackgroundColour2':'#18603B',
            'itemTextColour':'#FFFFFF',
            'rowBackgroundColour':'#2A8041',
            'alternateRowBackgroundColour':'#8AC453',
            'columnBackgroundColour':'#FFFFFF',
            'callToActionBackgroundColour1':'#605074',
            'callToActionBackgroundColour2':'#605074',
            'buttonTextColour':'#FFFFFF',
            'buttonBackgroundColour1':'#8AC453',
            'buttonBackgroundColour2':'#8AC453',
            'buttonBorderColour':'#B8B8B8',
            'fontFamily':'Open Sans',
            'textAlign':'center',
            'lineHeight':'21',
            'headingFontSize':'25',
            'pricingFontSize':'21',
            'rowFontSize':'16',
            'buttonFontSize':'',
            'headingCellHeight':'80',
            'headingCellPaddingTop':'34',
            'pricingCellHeight':'91',
            'pricingCellPaddingTop':'23',
            'cellHeight':'46',
            'cellPaddingTop':'13',
            'columnWidth':'200',
            'callToActionCellHeight':'65',
            'callToActionCellPaddingTop':'21',
            'buttonBorderPaddingTopBottom':'5',
            'buttonBorderPaddingLeftRight':'20',
            'buttonBorderStyle':'',
            'buttonBorderRadius':'5',
            'buttonBorderWidth':'',
            'borderColour':'#FFFFFF',
            'borderStyle':'solid',
            'borderWidth':'0',
            'borderRadius':'3',
            'cellBorderColour':'#B83737',
            'cellBorderStyle':'',
            'cellBorderWidth':'2',
            'cellBottomBorderWidth':'2',
            'cellBorderRadius':'0',
            'extra_css':''
        }
        presets.addPreset('preset3', preset3);

        var preset4 =
        {
            'margin':'0',
            'padding':'0',
            'priceOptions':'2',
            'featureColumn':'',
            'headingTextColour':'#18110B',
            'backgroundColour1':'#96AFC5',
            'backgroundColour2':'#96AFC5',
            'pricingTextColour':'#FFFFFF',
            'pricingBackgroundColour1':'#B23A39',
            'pricingBackgroundColour2':'#B23A39',
            'itemTextColour':'#18110B',
            'rowBackgroundColour':'#96AFC5',
            'alternateRowBackgroundColour':'#F6F6F6',
            'columnBackgroundColour':'#FFFFFF',
            'callToActionBackgroundColour1':'#18110B',
            'callToActionBackgroundColour2':'#18110B',
            'buttonTextColour':'#FFFFFF',
            'buttonBackgroundColour1':'#B23A39',
            'buttonBackgroundColour2':'#B23A39',
            'buttonBorderColour':'#B8B8B8',
            'fontFamily':'Open Sans',
            'textAlign':'center',
            'lineHeight':'21',
            'headingFontSize':'25',
            'pricingFontSize':'21',
            'rowFontSize':'16',
            'buttonFontSize':'',
            'headingCellHeight':'80',
            'headingCellPaddingTop':'34',
            'pricingCellHeight':'91',
            'pricingCellPaddingTop':'23',
            'cellHeight':'46',
            'cellPaddingTop':'13',
            'columnWidth':'200',
            'callToActionCellHeight':'65',
            'callToActionCellPaddingTop':'21',
            'buttonBorderPaddingTopBottom':'5',
            'buttonBorderPaddingLeftRight':'20',
            'buttonBorderStyle':'',
            'buttonBorderRadius':'5',
            'buttonBorderWidth':'',
            'borderColour':'#FFFFFF',
            'borderStyle':'solid',
            'borderWidth':'0',
            'borderRadius':'3',
            'cellBorderColour':'#B83737',
            'cellBorderStyle':'',
            'cellBorderWidth':'2',
            'cellBottomBorderWidth':'2',
            'cellBorderRadius':'0',
            'extra_css':''
        }
        presets.addPreset('preset4', preset4);

        var preset5 =
        {
            'margin':'0',
            'padding':'0',
            'priceOptions':'2',
            'featureColumn':'',
            'headingTextColour':'#FFFFFF',
            'backgroundColour1':'#511646',
            'backgroundColour2':'#511646',
            'pricingTextColour':'#FFFFFF',
            'pricingBackgroundColour1':'#5C4F85',
            'pricingBackgroundColour2':'#5C4F85',
            'itemTextColour':'#F9EABF',
            'rowBackgroundColour':'#280324',
            'alternateRowBackgroundColour':'#280324',
            'columnBackgroundColour':'#FFFFFF',
            'callToActionBackgroundColour1':'#280324',
            'callToActionBackgroundColour2':'#280324',
            'buttonTextColour':'#511646',
            'buttonBackgroundColour1':'#EE952D',
            'buttonBackgroundColour2':'#EE952D',
            'buttonBorderColour':'#B8B8B8',
            'fontFamily':'Open Sans',
            'textAlign':'center',
            'lineHeight':'21',
            'headingFontSize':'25',
            'pricingFontSize':'21',
            'rowFontSize':'16',
            'buttonFontSize':'',
            'headingCellHeight':'80',
            'headingCellPaddingTop':'34',
            'pricingCellHeight':'91',
            'pricingCellPaddingTop':'23',
            'cellHeight':'46',
            'cellPaddingTop':'13',
            'columnWidth':'200',
            'callToActionCellHeight':'65',
            'callToActionCellPaddingTop':'21',
            'buttonBorderPaddingTopBottom':'5',
            'buttonBorderPaddingLeftRight':'20',
            'buttonBorderStyle':'',
            'buttonBorderRadius':'5',
            'buttonBorderWidth':'',
            'borderColour':'#FFFFFF',
            'borderStyle':'solid',
            'borderWidth':'0',
            'borderRadius':'3',
            'cellBorderColour':'#B83737',
            'cellBorderStyle':'',
            'cellBorderWidth':'2',
            'cellBottomBorderWidth':'2',
            'cellBorderRadius':'0',
            'extra_css':''
        };
        presets.addPreset('preset5', preset5);

        var preset6 = {
            'margin':'0',
            'padding':'0',
            'priceOptions':'2',
            'featureColumn':'',
            'headingTextColour':'#FFFFFF',
            'backgroundColour1':'#B1292D',
            'backgroundColour2':'#B1292D',
            'pricingTextColour':'#FFFFFF',
            'pricingBackgroundColour1':'#0F0F0F',
            'pricingBackgroundColour2':'#0F0F0F',
            'itemTextColour':'#D1B23D',
            'rowBackgroundColour':'#000000',
            'alternateRowBackgroundColour':'#2B2B2B',
            'columnBackgroundColour':'#FFFFFF',
            'callToActionBackgroundColour1':'#000000',
            'callToActionBackgroundColour2':'#000000',
            'buttonTextColour':'#0F0F0F',
            'buttonBackgroundColour1':'#D1B23D',
            'buttonBackgroundColour2':'#D1B23D',
            'buttonBorderColour':'#B8B8B8',
            'fontFamily':'Open Sans',
            'textAlign':'center',
            'lineHeight':'21',
            'headingFontSize':'25',
            'pricingFontSize':'21',
            'rowFontSize':'16',
            'buttonFontSize':'',
            'headingCellHeight':'80',
            'headingCellPaddingTop':'34',
            'pricingCellHeight':'91',
            'pricingCellPaddingTop':'23',
            'cellHeight':'46',
            'cellPaddingTop':'13',
            'columnWidth':'200',
            'callToActionCellHeight':'65',
            'callToActionCellPaddingTop':'21',
            'buttonBorderPaddingTopBottom':'5',
            'buttonBorderPaddingLeftRight':'20',
            'buttonBorderStyle':'',
            'buttonBorderRadius':'5',
            'buttonBorderWidth':'0',
            'buttonShadowEffect':'',
            'borderColour':'#FFFFFF',
            'borderStyle':'solid',
            'borderWidth':'0',
            'borderRadius':'3',
            'cellBorderColour':'#B83737',
            'cellBorderStyle':'',
            'cellBorderWidth':'2',
            'cellBottomBorderWidth':'2',
            'cellBorderRadius':'0',
            'extra_css':''
        };
        presets.addPreset('preset6', preset6);

        var preset7 = {
            'margin':'0',
            'padding':'0',
            'priceOptions':'2',
            'featureColumn':'',
            'headingTextColour':'#FFFFFF',
            'backgroundColour1':'#F23C27',
            'backgroundColour2':'#F23C27',
            'pricingTextColour':'#FFFFFF',
            'pricingBackgroundColour1':'#1B729F',
            'pricingBackgroundColour2':'#1B729F',
            'itemTextColour':'#FFE27F',
            'rowBackgroundColour':'#000000',
            'alternateRowBackgroundColour':'#000000',
            'columnBackgroundColour':'#FFFFFF',
            'callToActionBackgroundColour1':'#000000',
            'callToActionBackgroundColour2':'#000000',
            'buttonTextColour':'#FFFFFF',
            'buttonBackgroundColour1':'#F23C27',
            'buttonBackgroundColour2':'#F23C27',
            'buttonBorderColour':'#B8B8B8',
            'fontFamily':'Open Sans',
            'textAlign':'center',
            'lineHeight':'21',
            'headingFontSize':'25',
            'pricingFontSize':'21',
            'rowFontSize':'16',
            'buttonFontSize':'',
            'headingCellHeight':'80',
            'headingCellPaddingTop':'34',
            'pricingCellHeight':'91',
            'pricingCellPaddingTop':'23',
            'cellHeight':'46',
            'cellPaddingTop':'13',
            'columnWidth':'200',
            'callToActionCellHeight':'65',
            'callToActionCellPaddingTop':'21',
            'buttonBorderPaddingTopBottom':'5',
            'buttonBorderPaddingLeftRight':'20',
            'buttonBorderStyle':'',
            'buttonBorderRadius':'5',
            'buttonBorderWidth':'0',
            'borderColour':'#FFFFFF',
            'borderStyle':'solid',
            'borderWidth':'0',
            'borderRadius':'3',
            'cellBorderColour':'#B83737',
            'cellBorderStyle':'',
            'cellBorderWidth':'2',
            'cellBottomBorderWidth':'2',
            'cellBorderRadius':'0',
            'extra_css':''
        };
        presets.addPreset('preset7', preset7);

        var preset8 = {
            'margin':'0',
            'padding':'0',
            'priceOptions':'2',
            'featureColumn':'',
            'headingTextColour':'#FFFFFF',
            'backgroundColour1':'#465767',
            'backgroundColour2':'#465767',
            'pricingTextColour':'#FFFFFF',
            'pricingBackgroundColour1':'#FBAA1A',
            'pricingBackgroundColour2':'#FBAA1A',
            'itemTextColour':'#000000',
            'rowBackgroundColour':'#E9E9E9',
            'alternateRowBackgroundColour':'#F2F1F7',
            'columnBackgroundColour':'#FFFFFF',
            'callToActionBackgroundColour1':'#FBAA1A',
            'callToActionBackgroundColour2':'#FBAA1A',
            'buttonTextColour':'#000000',
            'buttonBackgroundColour1':'#FBAA1A',
            'buttonBackgroundColour2':'#FBAA1A',
            'buttonBorderColour':'#000000',
            'fontFamily':'Open Sans',
            'textAlign':'center',
            'lineHeight':'21',
            'headingFontSize':'25',
            'pricingFontSize':'21',
            'rowFontSize':'16',
            'buttonFontSize':'',
            'headingCellHeight':'80',
            'headingCellPaddingTop':'34',
            'pricingCellHeight':'91',
            'pricingCellPaddingTop':'23',
            'cellHeight':'46',
            'cellPaddingTop':'13',
            'columnWidth':'200',
            'callToActionCellHeight':'65',
            'callToActionCellPaddingTop':'21',
            'buttonBorderPaddingTopBottom':'5',
            'buttonBorderPaddingLeftRight':'20',
            'buttonBorderStyle':'solid',
            'buttonBorderRadius':'5',
            'buttonBorderWidth':'3',
            'borderColour':'#FFFFFF',
            'borderStyle':'solid',
            'borderWidth':'0',
            'borderRadius':'3',
            'cellBorderColour':'#B83737',
            'cellBorderStyle':'',
            'cellBorderWidth':'2',
            'cellBottomBorderWidth':'2',
            'cellBorderRadius':'0',
            'extra_css':''
        };
        presets.addPreset('preset8', preset8);

        var preset9 = {
            'margin':'0',
            'padding':'0',
            'hoverEffectCheckbox':'yes',
            'priceOptions':'2',
            'featureColumn':'',
            'headingTextColour':'#FFFFFF',
            'backgroundColour1':'#465767',
            'backgroundColour2':'#465767',
            'pricingTextColour':'#FFFFFF',
            'pricingBackgroundColour1':'#F16161',
            'pricingBackgroundColour2':'#F16161',
            'itemTextColour':'#000000',
            'rowBackgroundColour':'#E9E9E9',
            'alternateRowBackgroundColour':'#F2F1F7',
            'columnBackgroundColour':'#FFFFFF',
            'callToActionBackgroundColour1':'#F16161',
            'callToActionBackgroundColour2':'#F16161',
            'buttonTextColour':'#FFFFFF',
            'buttonBackgroundColour1':'#F16161',
            'buttonBackgroundColour2':'#F16161',
            'buttonBorderColour':'#E9E9E9',
            'fontFamily':'Open Sans',
            'textAlign':'center',
            'lineHeight':'21',
            'headingFontSize':'25',
            'pricingFontSize':'21',
            'rowFontSize':'16',
            'buttonFontSize':'',
            'headingCellHeight':'80',
            'headingCellPaddingTop':'34',
            'pricingCellHeight':'91',
            'pricingCellPaddingTop':'23',
            'cellHeight':'46',
            'cellPaddingTop':'13',
            'columnWidth':'200',
            'callToActionCellHeight':'65',
            'callToActionCellPaddingTop':'21',
            'buttonBorderPaddingTopBottom':'5',
            'buttonBorderPaddingLeftRight':'20',
            'buttonBorderStyle':'solid',
            'buttonBorderRadius':'5',
            'buttonBorderWidth':'3',
            'borderColour':'#FFFFFF',
            'borderStyle':'solid',
            'borderWidth':'0',
            'borderRadius':'3',
            'cellBorderColour':'#B83737',
            'cellBorderStyle':'',
            'cellBorderWidth':'2',
            'cellBottomBorderWidth':'2',
            'cellBorderRadius':'0',
            'extra_css':''
        };
        presets.addPreset('preset9', preset9);

        var preset10 = {
            'margin':'0',
            'padding':'0',
            'priceOptions':'2',
            'featureColumn':'',
            'headingTextColour':'#FFFFFF',
            'backgroundColour1':'#DC5743',
            'backgroundColour2':'#DC5743',
            'pricingTextColour':'#FFFFFF',
            'pricingBackgroundColour1':'#000000',
            'pricingBackgroundColour2':'#000000',
            'itemTextColour':'#FFE27F',
            'rowBackgroundColour':'#000000',
            'alternateRowBackgroundColour':'#000000',
            'columnBackgroundColour':'#FFFFFF',
            'callToActionBackgroundColour1':'#000000',
            'callToActionBackgroundColour2':'#000000',
            'buttonTextColour':'#FFFFFF',
            'buttonBackgroundColour1':'#DC5743',
            'buttonBackgroundColour2':'#DC5743',
            'buttonBorderColour':'#B8B8B8',
            'fontFamily':'Open Sans',
            'textAlign':'center',
            'lineHeight':'21',
            'headingFontSize':'25',
            'pricingFontSize':'21',
            'rowFontSize':'16',
            'buttonFontSize':'',
            'headingCellHeight':'80',
            'headingCellPaddingTop':'34',
            'pricingCellHeight':'91',
            'pricingCellPaddingTop':'23',
            'cellHeight':'46',
            'cellPaddingTop':'13',
            'columnWidth':'200',
            'callToActionCellHeight':'65',
            'callToActionCellPaddingTop':'21',
            'buttonBorderPaddingTopBottom':'5',
            'buttonBorderPaddingLeftRight':'20',
            'buttonBorderStyle':'',
            'buttonBorderRadius':'5',
            'buttonBorderWidth':'',
            'borderColour':'#FFFFFF',
            'borderStyle':'solid',
            'borderWidth':'0',
            'borderRadius':'3',
            'cellBorderColour':'#B83737',
            'cellBorderStyle':'',
            'cellBorderWidth':'2',
            'cellBottomBorderWidth':'2',
            'cellBorderRadius':'0',
            'extra_css':''
        };

        presets.addPreset('preset10', preset10);

        var preset11 = {
            'margin':'0',
            'padding':'0',
            'hoverEffectCheckbox':'',
            'priceOptions':'2',
            'featureColumn':'',
            'headingTextColour':'#FFFFFF',
            'backgroundColour1':'#AE87C4',
            'backgroundColour2':'#AE87C4',
            'pricingTextColour':'#FFFFFF',
            'pricingBackgroundColour1':'#000000',
            'pricingBackgroundColour2':'#000000',
            'itemTextColour':'#FFE27F',
            'rowBackgroundColour':'#000000',
            'alternateRowBackgroundColour':'#000000',
            'columnBackgroundColour':'#FFFFFF',
            'callToActionBackgroundColour1':'#000000',
            'callToActionBackgroundColour2':'#000000',
            'buttonTextColour':'#FFFFFF',
            'buttonBackgroundColour1':'#AE87C4',
            'buttonBackgroundColour2':'#AE87C4',
            'buttonBorderColour':'#B8B8B8',
            'fontFamily':'Open Sans',
            'textAlign':'center',
            'lineHeight':'21',
            'headingFontSize':'25',
            'pricingFontSize':'21',
            'rowFontSize':'16',
            'buttonFontSize':'',
            'headingCellHeight':'80',
            'headingCellPaddingTop':'34',
            'pricingCellHeight':'91',
            'pricingCellPaddingTop':'23',
            'cellHeight':'46',
            'cellPaddingTop':'13',
            'columnWidth':'200',
            'callToActionCellHeight':'65',
            'callToActionCellPaddingTop':'21',
            'buttonBorderPaddingTopBottom':'5',
            'buttonBorderPaddingLeftRight':'20',
            'buttonBorderStyle':'',
            'buttonBorderRadius':'5',
            'buttonBorderWidth':'',
            'buttonShadowEffect':'',
            'borderColour':'#FFFFFF',
            'borderStyle':'solid',
            'borderWidth':'0',
            'borderRadius':'3',
            'cellBorderColour':'#B83737',
            'cellBorderStyle':'',
            'cellBorderWidth':'2',
            'cellBottomBorderWidth':'2',
            'cellBorderRadius':'0',
            'extra_css':''
        };

        presets.addPreset('preset11', preset11);

        var preset12 = {
            'margin':'0',
            'padding':'0',
            'priceOptions':'2',
            'featureColumn':'',
            'headingTextColour':'#FFFFFF',
            'backgroundColour1':'#3893C8',
            'backgroundColour2':'#3893C8',
            'pricingTextColour':'#FFFFFF',
            'pricingBackgroundColour1':'#000000',
            'pricingBackgroundColour2':'#000000',
            'itemTextColour':'#FFE27F',
            'rowBackgroundColour':'#000000',
            'alternateRowBackgroundColour':'#000000',
            'columnBackgroundColour':'#FFFFFF',
            'callToActionBackgroundColour1':'#000000',
            'callToActionBackgroundColour2':'#000000',
            'buttonTextColour':'#FFFFFF',
            'buttonBackgroundColour1':'#3893C8',
            'buttonBackgroundColour2':'#3893C8',
            'buttonBorderColour':'#B8B8B8',
            'fontFamily':'Open Sans',
            'textAlign':'center',
            'lineHeight':'21',
            'headingFontSize':'25',
            'pricingFontSize':'21',
            'rowFontSize':'16',
            'buttonFontSize':'',
            'headingCellHeight':'80',
            'headingCellPaddingTop':'34',
            'pricingCellHeight':'91',
            'pricingCellPaddingTop':'23',
            'cellHeight':'46',
            'cellPaddingTop':'13',
            'columnWidth':'200',
            'callToActionCellHeight':'65',
            'callToActionCellPaddingTop':'21',
            'buttonBorderPaddingTopBottom':'5',
            'buttonBorderPaddingLeftRight':'20',
            'buttonBorderStyle':'',
            'buttonBorderRadius':'5',
            'buttonBorderWidth':'',
            'borderColour':'#FFFFFF',
            'borderStyle':'solid',
            'borderWidth':'0',
            'borderRadius':'3',
            'cellBorderColour':'#B83737',
            'cellBorderStyle':'',
            'cellBorderWidth':'2',
            'cellBottomBorderWidth':'2',
            'cellBorderRadius':'0',
            'extra_css':''
        };

        presets.addPreset('preset12', preset12);

        var preset13 = {
            'margin':'0',
            'padding':'0',
            'priceOptions':'2',
            'featureColumn':'',
            'headingTextColour':'#FFFFFF',
            'backgroundColour1':'#8DB817',
            'backgroundColour2':'#8DB817',
            'pricingTextColour':'#FFFFFF',
            'pricingBackgroundColour1':'#000000',
            'pricingBackgroundColour2':'#000000',
            'itemTextColour':'#FFE27F',
            'rowBackgroundColour':'#000000',
            'alternateRowBackgroundColour':'#000000',
            'columnBackgroundColour':'#FFFFFF',
            'callToActionBackgroundColour1':'#000000',
            'callToActionBackgroundColour2':'#000000',
            'buttonTextColour':'#FFFFFF',
            'buttonBackgroundColour1':'#8DB817',
            'buttonBackgroundColour2':'#8DB817',
            'buttonBorderColour':'#B8B8B8',
            'fontFamily':'Open Sans',
            'textAlign':'center',
            'lineHeight':'21',
            'headingFontSize':'25',
            'pricingFontSize':'21',
            'rowFontSize':'16',
            'buttonFontSize':'',
            'headingCellHeight':'80',
            'headingCellPaddingTop':'34',
            'pricingCellHeight':'91',
            'pricingCellPaddingTop':'23',
            'cellHeight':'46',
            'cellPaddingTop':'13',
            'columnWidth':'200',
            'callToActionCellHeight':'65',
            'callToActionCellPaddingTop':'21',
            'buttonBorderPaddingTopBottom':'5',
            'buttonBorderPaddingLeftRight':'20',
            'buttonBorderStyle':'',
            'buttonBorderRadius':'5',
            'buttonBorderWidth':'0',
            'borderColour':'#FFFFFF',
            'borderStyle':'solid',
            'borderWidth':'0',
            'borderRadius':'3',
            'cellBorderColour':'#B83737',
            'cellBorderStyle':'',
            'cellBorderWidth':'2',
            'cellBottomBorderWidth':'2',
            'cellBorderRadius':'0',
            'extra_css':''
        };
        presets.addPreset('preset13', preset13);

        var preset14 = {
            'margin':'0',
            'padding':'0',
            'hoverEffectCheckbox':'',
            'priceOptions':'2',
            'featureColumn':'',
            'headingTextColour':'#FFFFFF',
            'backgroundColour1':'#FFAA00',
            'backgroundColour2':'#FFAA00',
            'pricingTextColour':'#FFFFFF',
            'pricingBackgroundColour1':'#000000',
            'pricingBackgroundColour2':'#000000',
            'itemTextColour':'#FFE27F',
            'rowBackgroundColour':'#000000',
            'alternateRowBackgroundColour':'#000000',
            'columnBackgroundColour':'#FFFFFF',
            'callToActionBackgroundColour1':'#000000',
            'callToActionBackgroundColour2':'#000000',
            'buttonTextColour':'#000000',
            'buttonBackgroundColour1':'#FFAA00',
            'buttonBackgroundColour2':'#FFAA00',
            'buttonBorderColour':'#B8B8B8',
            'fontFamily':'Open Sans',
            'textAlign':'center',
            'lineHeight':'21',
            'headingFontSize':'25',
            'pricingFontSize':'21',
            'rowFontSize':'16',
            'buttonFontSize':'',
            'headingCellHeight':'80',
            'headingCellPaddingTop':'34',
            'pricingCellHeight':'91',
            'pricingCellPaddingTop':'23',
            'cellHeight':'46',
            'cellPaddingTop':'13',
            'columnWidth':'200',
            'callToActionCellHeight':'65',
            'callToActionCellPaddingTop':'21',
            'buttonBorderPaddingTopBottom':'5',
            'buttonBorderPaddingLeftRight':'20',
            'buttonBorderStyle':'',
            'buttonBorderRadius':'5',
            'buttonBorderWidth':'0',
            'borderColour':'#FFFFFF',
            'borderStyle':'solid',
            'borderWidth':'0',
            'borderRadius':'3',
            'cellBorderColour':'#B83737',
            'cellBorderStyle':'',
            'cellBorderWidth':'2',
            'cellBottomBorderWidth':'2',
            'cellBorderRadius':'0',
            'extra_css':''
        };
        presets.addPreset('preset14', preset14);
    }
}

function store_content_hook() {
    return $('.storeContent').html();
}

function load_preset_content_hook(content) {
    if (content != '') {
        $('.storeContent').html(content);
    }
}

function store_js_hook() {
    var settings = new Object();

    if ($('#actionButton').length > 0) {
        settings.actionButton = $('#actionButton').is(":checked");
    }

    var data = JSON.stringify(settings);
    return data;
}

function load_preset_js_hook(js) {
    if (js != undefined) {
        var settings = JSON.parse(js);
        if ($('#actionButton').length > 0) {
            $('#actionButton').prop('checked', settings.actionButton);
        }
    }
}
