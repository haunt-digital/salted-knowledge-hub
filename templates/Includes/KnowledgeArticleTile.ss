<% with $cached %>
<div class="grid_3 knowledge-tile $HubClass">
    <a href="$Link" class="knowledge-tile__link">
        <div class="knowledge-tile__thumbnail">
            <img src="$Thumbnail.Small" />
            <span class="knowledge-tile__group-name">$HubGroup</span>
            <% if $HubClass == 'video' %>
            <div class="icon absolutely-center"><i class="fa fa-play"></i></div>
            <% end_if %>
        </div>
        <div class="knowledge-tile__content">
            <p class="knowledge-tile__date-published"><time datetime="$PublishedDate">$PublishedDate</time></p>
            <h3 class="knowledge-tile__title">$Title</h3>
            <% if $Author %><p class="knowledge-tile__author"><em>By $Author</em></p><% end_if %>
            <p class="knowledge-tile__excerpt">$Excerpt</p>
        </div>
    </a>
</div>
<% end_with %>
