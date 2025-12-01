<?php

require_once 'vendor/php-test-framework/public-api.php';
require_once 'vendor/php-test-framework/Employee.php';
require_once 'vendor/php-test-framework/Task.php';

const BASE_URL = 'http://localhost:8080/';
const WEBDRIVER_PATH = 'c:/Users/alice/chromedriver.exe';

test('Start page has menu with correct links', function () {

    navigateTo(BASE_URL);

    assertPageContainsLinkWithId('dashboard-link');
    assertPageContainsLinkWithId('employee-list-link');
    assertPageContainsLinkWithId('employee-form-link');
    assertPageContainsLinkWithId('task-list-link');
    assertPageContainsLinkWithId('task-form-link');
});

test('Can save employees', function () {
    navigateTo(BASE_URL);

    clickLinkWithId('employee-form-link');

    $employee = getSampleEmployee();

    setTextFieldValue('firstName', $employee->firstName);
    setTextFieldValue('lastName', $employee->lastName);

    clickButton('submitButton');

    waitPageText(fn() => containsStringOnce($employee->firstName));

    // check that data is not generated on server side

    useWebDriver(false);

    navigateTo(BASE_URL);

    assertThat(getPageText(), doesNotContainString($employee->firstName));

    useWebDriver(true);
});

test('Can update employees', function () {
    navigateTo(BASE_URL);

    clickLinkWithId('employee-form-link');

    $employee = getSampleEmployee();
    $newFirstName = getSampleEmployee()->firstName;

    setTextFieldValue('firstName', $employee->firstName);
    setTextFieldValue('lastName', $employee->lastName);

    clickButton('submitButton');

    $employeeId = getEmployeeIdByName(
        $employee->firstName . ' ' . $employee->lastName);

    clickLinkWithId('employee-edit-link-' . $employeeId);

    setTextFieldValue('firstName', $newFirstName);

    clickButton('submitButton');

    waitPageText(fn() => containsStringOnce($newFirstName));

    assertThat(getPageText(), doesNotContainString($employee->firstName));
});

test('Can delete inserted employees', function () {
    navigateTo(BASE_URL);

    clickLinkWithId('employee-form-link');

    $employee = getSampleEmployee();

    setTextFieldValue('firstName', $employee->firstName);
    setTextFieldValue('lastName', $employee->lastName);

    clickButton('submitButton');

    $employeeId = getEmployeeIdByName(
        $employee->firstName . ' ' . $employee->lastName);

    clickLinkWithId('employee-edit-link-' . $employeeId);

    clickButton('deleteButton');

    assertThat(getPageText(), doesNotContainString($employee->firstName));
});

function getWebDriverPath(): string {
    global $argv;

    if (count($argv) === 2) {
        return $argv[1];
    } else {
        return WEBDRIVER_PATH;
    }
}

setBaseUrl(BASE_URL);
setWebDriverPath(getWebDriverPath());
useWebDriver(true);
setPrintPageSourceOnError(false);
setShowBrowser(false);

stf\runTests(getPassFailReporter(4));