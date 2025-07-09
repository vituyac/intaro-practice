<pre><?php var_export($profileData); ?></pre>
<h1>Профиль пользователя</h1>
<form method="post" action="/profile">
    <label>Фамилия: <input name="lastName" value="<?= htmlspecialchars($profileData['lastName'] ?? '') ?>"></label><br>
    <label>Имя: <input name="firstName" value="<?= htmlspecialchars($profileData['firstName'] ?? '') ?>"></label><br>
    <label>Отчество: <input name="patronymic" value="<?= htmlspecialchars($profileData['patronymic'] ?? '') ?>"></label><br>
    <label>Email: <input name="email" value="<?= htmlspecialchars($profileData['email'] ?? '') ?>"></label><br>
    <label>Телефон: <input name="phone" value="<?= htmlspecialchars($profileData['phones'][0]['number'] ?? '') ?>"></label><br>
    <label>День рождения: <input name="birthday" value="<?= htmlspecialchars($profileData['birthday'] ?? '') ?>"></label><br>
    <label>Пол: <input name="sex" value="<?= htmlspecialchars($profileData['sex'] ?? '') ?>"></label><br>
    <label>Адрес доставки: <input name="address" value="<?= htmlspecialchars($profileData['address']['text'] ?? '') ?>"></label><br>
    <button type="submit">Сохранить</button>
</form>