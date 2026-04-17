{extends file="layouts/main.tpl"}
{block name="content"}
<div class="category-header">
    <a href="?controller=category" class="back-link">← Все категории</a>
    <h1>{$category.name}</h1>
    {if $category.description}
        <p class="category-description">{$category.description}</p>
    {/if}
</div>

<div class="category-toolbar">
    <p class="category-count">Всего статей: {count($articles)}</p>
    <div class="sort-buttons">
        <span>Сортировка:</span>
        <a href="?controller=category&action=view&id={$category.id}&sort=date{if $pagination.current > 1}&page={$pagination.current}{/if}" 
           class="btn btn-sm {if $pagination.sort == 'date'}btn-primary{else}btn-secondary{/if}">
            По дате
        </a>
        <a href="?controller=category&action=view&id={$category.id}&sort=views{if $pagination.current > 1}&page={$pagination.current}{/if}" 
           class="btn btn-sm {if $pagination.sort == 'views'}btn-primary{else}btn-secondary{/if}">
            По просмотрам
        </a>
    </div>
</div>

{if $articles}
    <div class="article-list">
        {foreach $articles as $article}
        <div class="article-item">
            {if $article.image}
                <img src="{$article.image}" alt="" class="article-image">
            {/if}
            <div class="article-content">
                <h3><a href="?controller=article&action=view&id={$article.id}">{$article.title}</a></h3>
                <p class="article-description">{$article.description|truncate:150|default:'Без описания'}</p>
                <div class="article-meta">
                    <span>👁 {$article.views} просмотров</span>
                    <span>📅 {$article.created_at|date_format:"%d.%m.%Y"}</span>
                    <a href="?controller=article&action=delete&id={$article.id}" class="btn btn-sm btn-danger" onclick="return confirm('Удалить статью?')">🗑️</a>
                </div>
            </div>
        </div>
        {/foreach}
    </div>

    {if $pagination.total > 1}
    <div class="pagination">
        {if $pagination.current > 1}
            <a href="?controller=category&action=view&id={$category.id}&page={$pagination.current - 1}&sort={$pagination.sort}" class="btn btn-secondary">←</a>
        {/if}
        
        {section name=p start=1 loop=$pagination.total + 1 step=1}
            {if $smarty.section.p.index == 1 || $smarty.section.p.index == $pagination.total || abs($smarty.section.p.index - $pagination.current) <= 1}
                <a href="?controller=category&action=view&id={$category.id}&page={$smarty.section.p.index}&sort={$pagination.sort}" 
                   class="btn {if $smarty.section.p.index == $pagination.current}btn-primary{else}btn-secondary{/if}">
                    {$smarty.section.p.index}
                </a>
            {elseif $smarty.section.p.index == 2 || $smarty.section.p.index == $pagination.total - 1}
                <span class="pagination-ellipsis">...</span>
            {/if}
        {/section}
        
        {if $pagination.current < $pagination.total}
            <a href="?controller=category&action=view&id={$category.id}&page={$pagination.current + 1}&sort={$pagination.sort}" class="btn btn-secondary">→</a>
        {/if}
    </div>
    {/if}
{else}
    <div class="empty-state">
        <p>📭 В этой категории пока нет статей.</p>
        <a href="?controller=article&action=showCreate" class="btn btn-primary">+ Добавить статью</a>
    </div>
{/if}
{/block}
