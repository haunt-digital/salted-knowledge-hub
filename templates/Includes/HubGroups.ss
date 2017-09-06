<nav class="knowledge-hub-groups">
    <div class="container is-flex<% if $BodyClass.LowerCase != 'knowledge-hub-group-page' && $BodyClass.LowerCase != 'knowledge-hub-landing-page' %> maintain-flex-on-mobile<% end_if %>">
        <div class="grid_9 is-flex hub-groups">
        <% if $ClassName != 'KnowledgeHubLandingPage' && $ClassName != 'KnowledgeHubGroupPage' %>
            <a class="grid grid_auto" href="$Parent.Link">&lt; To all $MyGroup</a>
        <% else %>
        <% loop $KnowledgeHubs %>
            <a class="grid grid_auto<% if $isActive %> is-active<% end_if %>" href="$Link">$Title</a>
        <% end_loop %>
        <% end_if %>
        </div>
        <div class="grid_3 is-flex align-horizontal-right search-holder">
            <form id="search-knowledge-hub" method="post" action="/api/v/1/knowledge-article">
                <input name="keywords" autocomplete="off" type="text" class="text" />
                <input name="csrf" type="hidden" value="$SecurityID" />
                <button type="submit" class="icon"><i class="fa fa-search"></i></button>
            </form>
        </div>
    </div>
</nav>
