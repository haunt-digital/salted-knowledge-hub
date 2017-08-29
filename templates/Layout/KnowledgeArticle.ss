<%-- <% include KnowledgeHubPageHeader %> --%>
<% include HubGroups %>
<% include KnowledgeArticleHero %>
<section class="section knowledge-article">
    <div class="container">
        <article class="knowledge-article__content grid_9">
            <div class="columns align-vertical-center is-marginless-vertical">
                <div class="column is-paddingless-vertical knowledge-article__content__group-name"><% if $BodyClass == 'video' || $BodyClass == 'case-study' %>$singular_name<% else %>$cached.PublishedDate<% end_if %></div>
                <div class="column is-narrow is-paddingless-vertical">
                    <div class="addthis_inline_share_toolbox"></div>
                </div>
            </div>
            <h1 class="knowledge-article__content__title title">$Title</h1>
            <% if $Author %>
            <p class="knowledge-article__content__author">By $Author.Title</p>
            <% else %>
            <hr />
            <% end_if %>
            <div class="knowledge-article__content__content">
                $Content
            </div>
        </article>
    </div>
</section>
<% if $Related %>
<section class="section related-articles">
    <div class="container">
        <h2 class="title grid_12">Keep reading:</h2>
        <div class="knowledge-tiles clearfix">
        <% loop $Related %>
            <% include KnowledgeArticleTile %>
        <% end_loop %>
        </div>
    </div>
</section>
<% end_if %>
