{extends file="layouts/main.tpl"}
{block name="content"}
<div class="form-container">
    <h1>Редактировать категорию</h1>
    <form action="?controller=category&action=update&id={$category.id}" method="POST">
        <div class="form-group">
            <label for="name">Название</label>
            <input type="text" id="name" name="name" value="{$category.name}" required>
        </div>
        <div class="form-group">
            <label for="description">Описание</label>
            <textarea id="description" name="description" rows="4">{$category.description}</textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Сохранить</button>
            <a href="?controller=category" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>
{/block}
