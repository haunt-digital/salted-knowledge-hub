Handlebars.registerHelper('ifEqual', function(a, b, options) {
    if(a === b) {
        return options.fn(this);
    }
    return options.inverse(this);
});

var knowledgeTiles   =  '{{#each list}}\
                        <div class="grid_{{#if BigTile}}6{{else}}3{{/if}} knowledge-tile {{HubClass}}">\
                            <a href="{{Link}}" class="knowledge-tile__link">\
                                <div class="knowledge-tile__thumbnail">\
                                    {{#if Thumbnail}}<img src="{{#if BigTile}}{{Thumbnail.Large}}{{else}}{{Thumbnail.Small}}{{/if}}" />{{/if}}\
                                    <span class="knowledge-tile__group-name">{{HubGroup}}</span>\
                                    {{#ifEqual HubClass "video"}}\
                                    <div class="icon absolutely-center"><i class="fa fa-play"></i></div>\
                                    {{/ifEqual}}\
                                </div>\
                                <div class="knowledge-tile__content">\
                                    <p class="knowledge-tile__date-published"><time datetime="{{PublishedDate}}">{{PublishedDate}}</time></p>\
                                    <h3 class="knowledge-tile__title">{{Title}}</h3>\
                                    {{#if Author}}<p class="knowledge-tile__author">By {{Author}}</p>{{/if}}\
                                    <p class="knowledge-tile__excerpt">{{Excerpt}}</p>\
                                </div>\
                            </a>\
                        </div>\
                        {{/each}}';
