(function($) {
    $(document).ready(function(e)
    {
        if ((typeof knowledge_base_class) == "undefined") {
            var knowledge_base_class = 'page-type-knowledge-hub-landing-page';
        }
        
        $('select.use-fancy').fancySelect().on('change.fs', function()
        {
            $(this).trigger('change.$');
        });

        $('.knowledge-hub.ajax-content').afetch(function(data, listTo, navTo)
        {
            listTo.addClass('filled');
            var template    =   Handlebars.compile(knowledgeTiles),
                tiles       =   template(data);

            tiles = $($.trim(tiles));

            $('.knowledge-hub__count .count').html(data.count);
            var unit        =   data.count > 1 ? $('.knowledge-hub__count .unit').data('plural') : $('.knowledge-hub__count .unit').data('singular');

            $('.knowledge-hub__count .unit').html(unit ? unit : ('Item' + (data.count > 1 ? 's' : '')));

            listTo.append(tiles);

            if (listTo.is('.is-masonry')) {
                if (!window.isotope) {
                    window.isotope = listTo = listTo.isotope(
                    {
                        getSortData: {
                            title: '.knowledge-tile__title',
                            date: '.knowledge-tile__date-published time'
                        },
                        stamp: '.grid_6',
                        itemSelector: '.knowledge-tile',
                        layoutMode: 'packery',
                        columnWidth: 60
                    });

                    listTo.imagesLoaded().progress( function()
                    {
                        listTo.isotope('layout');
                    });
                } else {
                    listTo.isotope( 'appended', tiles )
                          .imagesLoaded().progress( function()
                          {
                              listTo.isotope('layout');
                              var by = $('#knowledge-list-sorter option:selected').val();
                              listTo.isotope({ sortBy: by, sortAscending: by == 'date' ? false : true });
                          });
                }
            }

            if (data.pagination.href) {
                navTo.attr('href', data.pagination.href);
                navTo.removeClass('hide');
                navTo.parent().find('.nav-message').addClass('hide');
            } else {
                navTo.addClass('hide').attr('href', '');
                navTo.parent().find('.nav-message').removeClass('hide');
                navTo.parent().find('.nav-message').html(data.pagination.message);
            }

            navTo.parent().removeClass('hide');
        });

        $('#knowledge-list-sorter').change(function(e)
        {
            if (window.isotope) {
                var by = $(this).find('option:selected').val();
                window.isotope.isotope({ sortBy: by, sortAscending: by == 'date' ? false : true });
            }
        });

        if ($('#search-knowledge-hub').length == 1) {

            $('#search-knowledge-hub input.text').blur(function(e)
            {
                var pressingButton = $('#search-knowledge-hub button:active').length;

                if (!pressingButton) {
                    $(this).removeClass('focused');
                    $(this).val('');
                }
            }).keydown(function(e)
            {
                if (e.keyCode == 27) {
                    if ($.trim($(this).val()).length > 0) {
                        $(this).val('');
                    } else {
                        $(this).blur();
                    }
                }
            });

            $('#search-knowledge-hub').ajaxSubmit(
            {
                validator:  function()
                            {
                                if ($.trim($('#search-knowledge-hub input.text').val()).length == 0) {
                                    $('#search-knowledge-hub input.text').addClass('focused');
                                    setTimeout(function()
                                    {
                                        $('#search-knowledge-hub input.text').focus();
                                    }, 100);

                                    return false;
                                } else {
                                    // if (!$('body').hasClass('page-type-knowledge-hub-landing-page') && !$('body').hasClass('page-type-knowledge-hub-group-page')) {
                                    if (!$('body').hasClass(knowledge_base_class)) {
                                        location.href = '/knowledge-hub?keywords=' + $('#search-knowledge-hub input.text').val();
                                        return false;
                                    }
                                }
                                return true;
                            },

                onstart:    function()
                            {
                                $('.hub-groups .is-active, .hub-subcategories .is-active').removeClass('is-active');

                                $('#search-knowledge-hub input.text').blur().val('');
                                $('.ajax-list.filled').removeClass('filled');
                                $('.ajax-nav').addClass('hide');
                                $('.knowledge-hub__count .count').html('');
                                window.isotope.html('');
                            },

                success:    function(response)
                            {
                                window.isotope.addClass('filled');
                                var template    =   Handlebars.compile(knowledgeTiles),
                                    tiles       =   template(response),
                                    count       =   0;
                                    unit        =   null;

                                tiles           =   $($.trim(tiles));
                                count           =   response.count;

                                $('.knowledge-hub__count .count').html(count);
                                $('.knowledge-hub__count .unit').html('Item' + (count > 1 ? 's' : ''));

                                window.isotope.append(tiles)
                                      .isotope( 'appended', tiles )
                                      .imagesLoaded().progress( function()
                                      {
                                          window.isotope.isotope('layout');
                                          var by = $('#knowledge-list-sorter option:selected').val();
                                          window.isotope.isotope({ sortBy: by, sortAscending: by == 'date' ? false : true });
                                      });

                                if (response.pagination.href) {
                                    $('.ajax-nav .button').attr('href', response.pagination.href);
                                    $('.ajax-nav .button').removeClass('hide');
                                    $('.ajax-nav').find('.nav-message').addClass('hide');
                                } else {
                                    $('.ajax-nav .button').addClass('hide').attr('href', '');
                                    $('.ajax-nav').find('.nav-message').removeClass('hide');
                                    $('.ajax-nav').find('.nav-message').html(response.pagination.message);
                                }

                                $('.ajax-nav').removeClass('hide');

                            }
            });
        }
    });
})(jQuery);
