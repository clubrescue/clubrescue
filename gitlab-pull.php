<?php
// Define the full path where the .env.ini file is located.
// Comment out the variable selector in order to hardcode the envorinment file.
// Variable env.ini selector
$ini_array = parse_ini_file(dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . 'env.' . basename(dirname(__FILE__)) . '.ini');
// Hardcoded env.ini selector:
// $ini_array = parse_ini_file('/home/u70472p67165/domains/trb.nu/env.ini');
$secret = $ini_array["GITLAB_SECRET_WEBHOOK"];
$branch = $ini_array["CR_GIT_BRANCH"];
$pulltofolder = $ini_array["CR_GIT_PULLTO"];
$deployfolder = $ini_array["CR_WHITELABEL"];


if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST' && isset($_SERVER['HTTP_X_GITLAB_TOKEN']) && $_SERVER['HTTP_X_GITLAB_TOKEN'] === $secret) {
    // Make sure Content-Type is application/json
    $content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
    if (stripos($content_type, 'application/json') === 0) {
        // Read the input stream
		$body = file_get_contents("php://input");
		// Write the input stream for tshoot 
		file_put_contents('gitpostwebhook.json', file_get_contents('php://input'));
		// Decode the JSON object
        $object = json_decode($body, true);
        // Throw an exception if decoding failed
        if (is_array($object)) {
            // Display the object (for merged branches state must be merged for development event must be push to the refs/heads/dev branch.
            if ($object['object_attributes']['state'] === 'merged' || $object['event_name'] === 'push' && $object['ref'] === 'refs/heads/dev') {
                // execute the shell command to pull latest from GitLab
                exec('cd ~/'.$pulltofolder.'/'.$deployfolder.'/ && git fetch --all && git reset --hard origin/'.$branch, $a, $b);
                //init database
                require_once $_SERVER['DOCUMENT_ROOT'] . '/'.$deployfolder.'/util/utility.class.php'; // gitlab-pull will fail if file does not exist.
                require_once $_SERVER['DOCUMENT_ROOT'] . '/'.$deployfolder.'/util/database.class.php'; // gitlab-pull will fail if file does not exist.
                // Logging should be done for Ruud his version check.
                echo 'Exitcode: '.$b;
                $database = new Database();
				if ($b === 0) {
                    // Update MergeCommitSha Hash in Database Options Table
					if ($object['object_attributes']['state'] === 'merged') {
						$updateQuery = 'UPDATE `cr_options` SET `LastGitlabCommit` = \''.$object['object_attributes']['merge_commit_sha'].'\' WHERE `cr_options`.`ID` = 1;';
                    } elseif ($object['event_name'] === 'push' && $object['ref'] === 'refs/heads/dev') {
						$updateQuery = 'UPDATE `cr_options` SET `LastGitlabCommit` = \''.$object['checkout_sha'].'\' WHERE `cr_options`.`ID` = 1;';
                    } else {
						$updateQuery = ''; // No merge (or push event for development) have occurred
					}
					echo $updateQuery;
                    $database->query($updateQuery);
                    $updateResult = $database->execute();
                }
                echo implode(' & ', $a);
                // Insert exec() output into the logging table
                $logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \'GitLab Webhook exited with status: '.$b.'. Output of the commands are: '.addslashes(implode(' & ', $a)).'.\', \'GitLab\')';
                echo $logQuery;
                $database->query($logQuery);
                $database->execute();
            } else {
                echo 'Invalid state, only merge is accepted (or push on development envorinment).';
            }
        } else {
            http_response_code(403);
        }
    } else {
        http_response_code(403);
    }
} else {
    http_response_code(403);
}