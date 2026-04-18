{extends file="layouts/main.tpl"}
{block name="content"}
<script>
function validateCategories() {
    const checked = document.querySelectorAll('input[name="categories[]"]:checked');
    if (checked.length === 0) {
        alert('Выберите хотя бы одну категорию');
        return false;
    }
    return true;
}
</script>
<div class="form-container">
    <h1>Редактировать статью</h1>
    <form action="?controller=article&action=update&id={$article.id}" method="POST" enctype="multipart/form-data" onsubmit="return validateCategories()">
        <div class="form-group">
            <label for="image">Изображение</label>
            {if $article.image}
                <div class="current-image">
                    <img src="{$article.image}" alt="" class="current-image-preview">
                </div>
            {/if}
            <input type="file" id="image" name="image" accept="image/*">
            <small class="form-hint">Оставьте пустым, чтобы оставить текущее изображение</small>
        </div>
        <div class="form-group">
            <label for="title">Название *</label>
            <input type="text" id="title" name="title" value="{$article.title}" required>
        </div>
        <div class="form-group">
            <label for="description">Краткое описание</label>
            <textarea id="description" name="description" rows="2">{$article.description}</textarea>
        </div>
        <div class="form-group">
            <label for="content">Текст статьи</label>
            <textarea id="content" name="content" rows="8">{$article.content}</textarea>
        </div>
        <div class="form-group">
            <label>Категории *</label>
            <div class="checkbox-group">
                {foreach $categories as $category}
                    <label class="checkbox-label">
                        <input type="checkbox" name="categories[]" value="{$category.id}"
                            {if in_array($category.id, explode(',', $article.category_ids))}checked{/if}>
                        <span>{$category.name}</span>
                    </label>
                {/foreach}
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Сохранить</button>
            <a href="?controller=category" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>
{/block}
