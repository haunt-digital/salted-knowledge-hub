<nav class="knowledge-hub-subcategory">
    <div class="container is-flex">
        <div class="grid_12 is-flex hub-subcategories">
        <% loop $SubCategories %>
            <a class="grid grid_auto<% if $isActive %> is-active<% end_if %>" href="$Link">$Title</a>
        <% end_loop %>
    </div>
</nav>
