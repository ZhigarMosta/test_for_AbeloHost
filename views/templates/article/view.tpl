{extends file="layouts/main.tpl"}
{block name="content"}
<div class="article-view">
    <div class="article-hero">
        <div class="article-actions">
            <a href="?controller=category" class="back-link">← Все категории</a>
            <a href="?controller=article&action=showEdit&id={$article.id}" class="btn btn-secondary">✏️ Редактировать</a>
        </div>

        {if $article.image}
            <img src="{$article.image}" alt="{$article.title}" class="article-image">
        {/if}

        <div class="article-header">
            <h1>{$article.title}</h1>
            <div class="article-meta">
                <span>👁 {$article.views} просмотров</span>
                <span>📅 {$article.created_at|date_format:"%d.%m.%Y"}</span>
            </div>
        </div>

        {if $article.description}
            <p class="article-description">{$article.description}</p>
        {/if}

        {if $article.content}
            <div class="article-content">{$article.content|nl2br}</div>
        {/if}
    </div>

    {if $relatedArticles}
    <div class="related-articles">
        <h2>📌 Похожие статьи</h2>
        <div class="article-grid">
            {foreach $relatedArticles as $related}
            <div class="article-card">
                {if $related.image}
                    <img src="{$related.image}" alt="">
                {/if}
                <div>
                    <h3><a href="?controller=article&action=view&id={$related.id}">{$related.title}</a></h3>
                    <p>👁 {$related.views}</p>
                </div>
            </div>
            {/foreach}
        </div>
    </div>
    {/if}
</div>
{/block}
