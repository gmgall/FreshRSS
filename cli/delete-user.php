#!/usr/bin/env php
<?php
declare(strict_types=1);
require(__DIR__ . '/_cli.php');

performRequirementCheck(FreshRSS_Context::systemConf()->db['type'] ?? '');

$parameters = [
	'long' => [
		'user' => ':',
	],
	'short' => [],
	'deprecated' => [],
];

$options = parseCliParams($parameters);

if (!empty($options['invalid']) || empty($options['valid']['user']) || !is_string($options['valid']['user'])) {
	fail('Usage: ' . basename(__FILE__) . " --user username");
}
$username = $options['valid']['user'];
if (!FreshRSS_user_Controller::checkUsername($username)) {
	fail('FreshRSS error: invalid username “' . $username . '”');
}

$usernames = listUsers();
if (!preg_grep("/^$username$/i", $usernames)) {
	fail('FreshRSS error: username not found “' . $username . '”');
}

if (strcasecmp($username, FreshRSS_Context::systemConf()->default_user) === 0) {
	fail('FreshRSS error: default user must not be deleted: “' . $username . '”');
}

echo 'FreshRSS deleting user “', $username, "”…\n";

$ok = FreshRSS_user_Controller::deleteUser($username);

invalidateHttpCache(FreshRSS_Context::systemConf()->default_user);

done($ok);
