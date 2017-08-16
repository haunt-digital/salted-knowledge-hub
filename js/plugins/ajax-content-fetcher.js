(function($) {
    $.fn.afetch = function(cbf)
    {
        var self            =   $(this),
            callback        =   cbf,
            fetch           =   function(endpoint, listTo, navTo)
                                {
                                    $.get(
                                        endpoint,
                                        function(data)
                                        {
                                            if (cbf) {
                                                cbf(data, listTo, navTo);
                                            }
                                        }
                                    );
                                };

        $(this).each(function(i, el)
        {
            var me          =   $(this),
                endpoint    =   me.data('endpoint'),
                listTo      =   me.find('.ajax-list'),
                navTo       =   me.find('.ajax-nav a.button');

            fetch(endpoint, listTo, navTo);
            navTo.unbind('click').click(function(e)
            {
                e.preventDefault();
                var endpoint    =   $(this).attr('href');
                if (endpoint.length > 0) {
                    fetch(endpoint, listTo, navTo);
                }
            });
        });

        return self;
    };
})(jQuery);
