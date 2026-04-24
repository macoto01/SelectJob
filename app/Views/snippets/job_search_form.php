<form class="search-form" method="get" action="<?= h(base_url('/jobs')) ?>">
  <input type="text" name="keyword" value="<?= h($filters['keyword']??'') ?>" placeholder="職種・会社名・スキルで検索" class="search-input">
  <input type="text" name="location" value="<?= h($filters['location']??'') ?>" placeholder="勤務地" class="search-input search-input--sm">
  <label class="check-label"><input type="checkbox" name="remote" value="1" <?= !empty($filters['remote'])?'checked':'' ?>> リモート可</label>
  <label class="check-label"><input type="checkbox" name="flex" value="1" <?= !empty($filters['flex'])?'checked':'' ?>> フレックス</label>
  <button type="submit" class="btn-search">検索</button>
</form>
