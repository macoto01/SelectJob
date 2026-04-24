<?php
$activeNav = 'resume';
$sectionLabels = ['basic'=>'基本情報','desired'=>'希望条件','skill'=>'スキル','career'=>'職務経歴','education'=>'学歴','award'=>'表彰','language'=>'語学','qualification'=>'資格'];
$editId = (int)($_GET['id'] ?? 0);
$editRow = [];
if ($editId > 0 && in_array($section, ['career', 'education', 'award', 'language', 'qualification'])) {
    // ResumeModelのfindListRowByIdメソッドを使用
    $editRow = $this->model->findListRowById('resume_' . $section, $editId);
}
?>
<div class="resume-layout">
  <nav class="resume-nav">
    <?php foreach ($sectionLabels as $key => $label): ?>
      <a href="<?= h(base_url('/resume/edit?section='.$key)) ?>" class="resume-nav-item<?= $section===$key?' active':'' ?>"><?= h($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <div class="resume-body">
    <div class="resume-section-header">
      <h2 class="resume-section-title"><?= h($sectionLabels[$section]??'') ?>の編集</h2>
      <a href="<?= h(base_url('/resume?section='.$section)) ?>" class="btn-cancel">閲覧に戻る</a>
    </div>
    <form method="post" action="<?= h(base_url('/resume/save')) ?>">
<?php require BASE_PATH . '/app/Views/snippets/csrf.php'; ?>
      <input type="hidden" name="section" value="<?= h($section) ?>">
      <table class="resume-form-table">

      <?php if ($section==='basic'): ?>
        <tr><th>姓</th><td><div class="form-inline"><input type="text" name="last_name" value="<?= h($basic['last_name']??'') ?>" class="form-input" placeholder="山田"><input type="text" name="first_name" value="<?= h($basic['first_name']??'') ?>" class="form-input" placeholder="太郎"></div></td></tr>
        <tr><th>姓（カナ）</th><td><div class="form-inline"><input type="text" name="last_name_kana" value="<?= h($basic['last_name_kana']??'') ?>" class="form-input" placeholder="ヤマダ"><input type="text" name="first_name_kana" value="<?= h($basic['first_name_kana']??'') ?>" class="form-input" placeholder="タロウ"></div></td></tr>
        <tr><th>性別 <span class="badge-required">必須</span></th><td><select name="gender" class="form-select"><?php foreach (['','男性','女性','その他'] as $g):?><option <?= ($basic['gender']??'')===$g?'selected':'' ?>><?= h($g) ?></option><?php endforeach;?></select></td></tr>
        <tr><th>生年月日<span class="lock-badge">🔒</span></th><td><input type="date" name="birthdate" value="<?= h($basic['birthdate']??'') ?>" class="form-input form-input--sm"></td></tr>
        <tr><th>電話番号 <span class="badge-required">必須</span><span class="lock-badge">🔒</span></th><td><input type="tel" name="phone" value="<?= h($basic['phone']??'') ?>" class="form-input form-input--sm"></td></tr>
        <tr><th>住所<span class="lock-badge">🔒</span></th>
          <td><div class="form-stack">
            <label class="form-label">郵便番号</label>
            <div class="form-inline"><input type="text" name="zip" id="zip" value="<?= h($basic['zip']??'') ?>" placeholder="例: 1500002（ハイフン不要）" class="form-input form-input--sm" maxlength="8" inputmode="numeric"><button type="button" id="zip-search-btn" class="btn-zip-search">住所を自動入力</button></div>
            <p id="zip-msg" class="form-hint" style="display:none;"></p>
            <label class="form-label">都道府県</label><input type="text" name="address_pref" id="address_pref" value="<?= h($basic['address_pref']??'') ?>" placeholder="例: 東京都" class="form-input form-input--sm">
            <label class="form-label">市区町村</label><input type="text" name="address_city" id="address_city" value="<?= h($basic['address_city']??'') ?>" placeholder="例: 渋谷区" class="form-input form-input--sm">
            <label class="form-label">町域・番地</label><input type="text" name="address_street" id="address_street" value="<?= h($basic['address_street']??'') ?>" placeholder="例: 渋谷1-1-1" class="form-input">
            <label class="form-label">建物名・部屋番号</label><input type="text" name="address_building" value="<?= h($basic['address_building']??'') ?>" placeholder="例: ○○マンション101" class="form-input">
          </div></td>
        </tr>
        <tr><th>現在の年収 <span class="badge-required">必須</span></th><td><div class="form-inline"><input type="number" name="salary" value="<?= h($basic['salary']??'') ?>" class="form-input form-input--sm"><span>万円</span></div></td></tr>

      <?php elseif ($section==='desired'): ?>
        <tr><th>希望職種</th><td><input type="text" name="desired_job" value="<?= h($desired['desired_job']??'') ?>" class="form-input"></td></tr>
        <tr><th>希望勤務地</th><td><input type="text" name="desired_location" value="<?= h($desired['desired_location']??'') ?>" class="form-input"></td></tr>
        <tr><th>希望年収</th><td><div class="form-inline"><input type="number" name="desired_salary_min" value="<?= h($desired['desired_salary_min']??'') ?>" class="form-input form-input--sm" placeholder="最低"><span>〜</span><input type="number" name="desired_salary_max" value="<?= h($desired['desired_salary_max']??'') ?>" class="form-input form-input--sm" placeholder="最高"><span>万円</span></div></td></tr>
        <tr><th>入社可能時期</th><td><input type="text" name="desired_start" value="<?= h($desired['desired_start']??'') ?>" class="form-input" placeholder="例: 即日 / 3ヶ月後"></td></tr>
        <tr><th>転職理由</th><td><textarea name="change_reason" rows="5" class="form-textarea"><?= h($desired['change_reason']??'') ?></textarea></td></tr>
        <tr><th>自己PR</th><td><textarea name="appeal" rows="8" class="form-textarea"><?= h($desired['appeal']??'') ?></textarea></td></tr>

      <?php elseif ($section==='skill'): ?>
        <tr><th>ITスキル</th><td><textarea name="it_skills" rows="5" class="form-textarea" placeholder="例: PHP/Laravel 5年、MySQL 4年"><?= h($skill['it_skills']??'') ?></textarea></td></tr>
        <tr><th>語学</th><td><textarea name="languages" rows="3" class="form-textarea" placeholder="例: 英語（ビジネス）、TOEIC 800点"><?= h($skill['languages']??'') ?></textarea></td></tr>
        <tr><th>資格・免許</th><td><textarea name="certifications" rows="3" class="form-textarea"><?= h($skill['certifications']??'') ?></textarea></td></tr>
        <tr><th>その他スキル</th><td><textarea name="other_skills" rows="3" class="form-textarea"><?= h($skill['other_skills']??'') ?></textarea></td></tr>

      <?php elseif ($section==='career'): ?>
        <?php if ($editId > 0): ?><input type="hidden" name="id" value="<?= $editId ?>"><?php endif; ?>
        <tr><th>会社名</th><td><input type="text" name="company_name" value="<?= h($editRow['company_name']??'') ?>" class="form-input"></td></tr>
        <tr><th>雇用形態</th><td><select name="employment_type" class="form-select"><?php foreach(['','正社員','契約社員','派遣社員','業務委託','パート'] as $et):?><option <?= ($editRow['employment_type']??'')===$et?'selected':'' ?>><?= h($et) ?></option><?php endforeach;?></select></td></tr>
        <tr><th>在籍期間</th><td><div class="form-inline"><input type="number" name="start_year" value="<?= h($editRow['start_year']??'') ?>" class="form-input form-input--sm" placeholder="年"><input type="number" name="start_month" value="<?= h($editRow['start_month']??'') ?>" class="form-input form-input--sm" placeholder="月" min="1" max="12"><span>〜</span><input type="number" name="end_year" value="<?= h($editRow['end_year']??'') ?>" class="form-input form-input--sm" placeholder="年"><input type="number" name="end_month" value="<?= h($editRow['end_month']??'') ?>" class="form-input form-input--sm" placeholder="月" min="1" max="12"><label class="check-label"><input type="checkbox" name="is_current" value="1" <?= ($editRow['is_current']??0)?'checked':'' ?>> 現在</label></div></td></tr>
        <tr><th>役職</th><td><input type="text" name="position" value="<?= h($editRow['position']??'') ?>" class="form-input"></td></tr>
        <tr><th>業務内容</th><td><textarea name="description" rows="8" class="form-textarea"><?= h($editRow['description']??'') ?></textarea></td></tr>
        <tr><th>海外経験</th><td><div class="form-inline"><input type="text" name="overseas_country" value="<?= h($editRow['overseas_country']??'') ?>" placeholder="国名" class="form-input form-input--sm"><input type="text" name="overseas_period" value="<?= h($editRow['overseas_period']??'') ?>" placeholder="期間" class="form-input form-input--sm"><input type="text" name="overseas_purpose" value="<?= h($editRow['overseas_purpose']??'') ?>" placeholder="目的" class="form-input form-input--sm"></div></td></tr>
        <tr><th>表示順</th><td><input type="number" name="sort_order" value="<?= h($editRow['sort_order']??0) ?>" class="form-input form-input--sm"></td></tr>

      <?php elseif ($section==='education'): ?>
        <?php if ($editId > 0): ?><input type="hidden" name="id" value="<?= $editId ?>"><?php endif; ?>
        <tr><th>学校名</th><td><input type="text" name="school_name" value="<?= h($editRow['school_name']??'') ?>" class="form-input"></td></tr>
        <tr><th>学部・学科</th><td><div class="form-inline"><input type="text" name="faculty" value="<?= h($editRow['faculty']??'') ?>" class="form-input" placeholder="学部"><input type="text" name="major" value="<?= h($editRow['major']??'') ?>" class="form-input" placeholder="学科"></div></td></tr>
        <tr><th>在学期間</th><td><div class="form-inline"><input type="number" name="start_year" value="<?= h($editRow['start_year']??'') ?>" class="form-input form-input--sm" placeholder="年"><input type="number" name="start_month" value="<?= h($editRow['start_month']??'') ?>" class="form-input form-input--sm" placeholder="月" min="1" max="12"><span>〜</span><input type="number" name="end_year" value="<?= h($editRow['end_year']??'') ?>" class="form-input form-input--sm" placeholder="年"><input type="number" name="end_month" value="<?= h($editRow['end_month']??'') ?>" class="form-input form-input--sm" placeholder="月" min="1" max="12"><label class="check-label"><input type="checkbox" name="is_current" value="1" <?= ($editRow['is_current']??0)?'checked':'' ?>> 在学中</label></div></td></tr>
        <tr><th>学位</th><td><input type="text" name="degree" value="<?= h($editRow['degree']??'') ?>" class="form-input form-input--sm" placeholder="例: 学士（工学）"></td></tr>

      <?php elseif ($section==='award'): ?>
        <?php if ($editId > 0): ?><input type="hidden" name="id" value="<?= $editId ?>"><?php endif; ?>
        <tr><th>受賞タイトル</th><td><input type="text" name="title" value="<?= h($editRow['title']??'') ?>" class="form-input"></td></tr>
        <tr><th>主催団体</th><td><input type="text" name="organization" value="<?= h($editRow['organization']??'') ?>" class="form-input"></td></tr>
        <tr><th>受賞日</th><td><input type="text" name="award_date" value="<?= h($editRow['award_date']??'') ?>" class="form-input form-input--sm" placeholder="例: 2023年3月"></td></tr>
        <tr><th>詳細</th><td><textarea name="description" rows="4" class="form-textarea"><?= h($editRow['description']??'') ?></textarea></td></tr>

      <?php elseif ($section==='language'): ?>
        <?php if ($editId > 0): ?><input type="hidden" name="id" value="<?= $editId ?>"><?php endif; ?>
        <tr><th>言語</th><td><input type="text" name="language" value="<?= h($editRow['language']??'') ?>" class="form-input" placeholder="例: 英語"></td></tr>
        <tr><th>レベル</th><td><select name="level" class="form-select"><?php foreach(['','日常会話レベル','ビジネスレベル','ネイティブレベル'] as $l):?><option <?= ($editRow['level']??'')===$l?'selected':'' ?>><?= h($l) ?></option><?php endforeach;?></select></td></tr>
        <tr><th>試験名</th><td><input type="text" name="test_name" value="<?= h($editRow['test_name']??'') ?>" class="form-input form-input--sm" placeholder="例: TOEIC"></td></tr>
        <tr><th>スコア</th><td><input type="text" name="test_score" value="<?= h($editRow['test_score']??'') ?>" class="form-input form-input--sm" placeholder="例: 800点"></td></tr>

      <?php elseif ($section==='qualification'): ?>
        <?php if ($editId > 0): ?><input type="hidden" name="id" value="<?= $editId ?>"><?php endif; ?>
        <tr><th>資格名</th><td><input type="text" name="name" value="<?= h($editRow['name']??'') ?>" class="form-input"></td></tr>
        <tr><th>取得日</th><td><div class="form-inline"><input type="number" name="acquired_year" value="<?= h($editRow['acquired_year']??'') ?>" class="form-input form-input--sm" placeholder="年"><input type="number" name="acquired_month" value="<?= h($editRow['acquired_month']??'') ?>" class="form-input form-input--sm" placeholder="月" min="1" max="12"></div></td></tr>
        <tr><th>取得予定</th><td><label class="check-label"><input type="checkbox" name="in_progress" value="1" <?= ($editRow['in_progress']??0)?'checked':'' ?>> 取得予定</label></td></tr>
      <?php endif; ?>

      </table>
      <div class="form-actions">
        <a href="<?= h(base_url('/resume?section='.$section)) ?>" class="btn-cancel">キャンセル</a>
        <button type="submit" class="btn-save">保存する</button>
      </div>
    </form>
  </div>
</div>
