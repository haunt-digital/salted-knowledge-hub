<%-- <% include KnowledgeHubPageHeader %> --%>
<% include HubGroups %>
<% if $SubCategories %>
<% include SubCategory %>
<% end_if %>
<div class="container ajax-content knowledge-hub" data-endpoint="/api/v/1/knowledge-article/$GroupID<% if $appendingVars %>$appendingVars<% end_if %>">
    <div class="is-flex knowledge-hub__count-filter align-vertical-center">
        <div class="grid_8 knowledge-hub__count">
            <span class="count"></span>
            <span class="unit" data-singular="$Singular" data-plural="$Plural"></span>
        </div>
        <div class="grid_4 is-flex knowledge-hub__filter align-horizontal-right">
            <%-- <div class="select is-long is-cap is-tall"> --%>
                <select id="knowledge-list-sorter" class="use-fancy hidden">
                    <option selected value="date">Most recent</option>
                    <option value="title">Sort by title</option>
                </select>
            <%-- </div> --%>
        </div>
    </div>
    <div class="knowledge-tiles is-masonry ajax-list clearfix"></div>
    <div class="ajax-nav hide">
        <a href="" class="button hide">Load more</a>
        <p class="nav-message hide"></p>
    </div>
</div>
