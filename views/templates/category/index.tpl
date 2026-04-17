{extends file="layouts/main.tpl"}
{block name="content"}
<div class="header-actions">
    <h1>📚 Категории и статьи</h1>
    <div class="header-buttons">
        <a href="?controller=article&action=showCreate" class="btn btn-primary">+ Добавить статью</a>
<a href="?controller=category&action=showCreate" class="btn btn-primary">+ Добавить категорию</a>
    </div>
</div>

{foreach $categories as $category}
<div class="category-section">
    <div class="category-section-header">
        <h2>📁 {$category.name}</h2>
        <div class="category-section-actions">
            <a href="?controller=category&action=view&id={$category.id}" class="btn btn-secondary btn-sm">Все статьи ({$category.article_count})</a>
            <a href="?controller=category&action=showEdit&id={$category.id}" class="category-edit-link">✏️</a>
        </div>
    </div>
    
    <div class="category-articles-grid">
        {foreach $category.articles as $article}
        <div class="article-card">
            {if $article.image}
                <img src="{$article.image}" alt="" class="article-card-image">
            {/if}
            <div class="article-card-body">
                <h3><a href="?controller=article&action=view&id={$article.id}">{$article.title}</a></h3>
                <p class="article-card-description">{$article.description|truncate:80|default:'Без описания'}</p>
                <div class="article-card-footer">
                    <span>👁 {$article.views}</span>
                </div>
            </div>
        </div>
        {/foreach}
    </div>
</div>
{/foreach}

{if empty($categories)}
<div class="empty-state">
    <p>📭 Категорий пока нет.</p>
    <a href="?controller=category&action=create" class="btn btn-primary">+ Добавить категорию</a>
</div>
{/if}
{/block}
