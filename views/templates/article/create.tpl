{extends file="layouts/main.tpl"}
{block name="content"}
<div class="form-container">
    <h1>Добавить статью</h1>
    <form action="?controller=article&action=store" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="image">Изображение</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <div class="form-group">
            <label for="title">Название *</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="description">Краткое описание</label>
            <textarea id="description" name="description" rows="2" placeholder="Краткое описание статьи..."></textarea>
        </div>
        <div class="form-group">
            <label for="content">Текст статьи</label>
            <textarea id="content" name="content" rows="8" placeholder="Полный текст статьи..."></textarea>
        </div>
        <div class="form-group">
            <label>Категории</label>
            <div class="checkbox-group">
                {foreach $categories as $category}
                <label class="checkbox-label">
                    <input type="checkbox" name="categories[]" value="{$category.id}">
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
