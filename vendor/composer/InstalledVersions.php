<?php











namespace Composer;

use Composer\Autoload\ClassLoader;
use Composer\Semver\VersionParser;








class InstalledVersions
{
private static $installed = array (
  'root' => 
  array (
    'pretty_version' => 'dev-master',
    'version' => 'dev-master',
    'aliases' => 
    array (
      0 => '3.1.x-dev',
    ),
    'reference' => '239cf03ce849273a00f47888141e4922b26fc30e',
    'name' => 'james/vaultbear',
  ),
  'versions' => 
  array (
    'cordoval/hamcrest-php' => 
    array (
      'replaced' => 
      array (
        0 => '*',
      ),
    ),
    'davedevelopment/hamcrest-php' => 
    array (
      'replaced' => 
      array (
        0 => '*',
      ),
    ),
    'guzzlehttp/guzzle' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '7.4.x-dev',
      ),
      'reference' => 'de6f1e58e735754b888649495ed4cb9ae3b19589',
    ),
    'guzzlehttp/promises' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '1.4.x-dev',
      ),
      'reference' => '8e7d04f1f6450fef59366c399cfad4b9383aa30d',
    ),
    'guzzlehttp/psr7' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '2.0.x-dev',
      ),
      'reference' => '1dc8d9cba3897165e16d12bb13d813afb1eb3fe7',
    ),
    'hamcrest/hamcrest-php' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '2.1.x-dev',
      ),
      'reference' => '8c3d0a3f6af734494ad8f6fbbee0ba92422859f3',
    ),
    'james/vaultbear' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '3.1.x-dev',
      ),
      'reference' => '239cf03ce849273a00f47888141e4922b26fc30e',
    ),
    'kodova/hamcrest-php' => 
    array (
      'replaced' => 
      array (
        0 => '*',
      ),
    ),
    'mockery/mockery' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '1.4.x-dev',
      ),
      'reference' => 'd1339f64479af1bee0e82a0413813fe5345a54ea',
    ),
    'nette/caching' => 
    array (
      'pretty_version' => 'v3.1.x-dev',
      'version' => '3.1.9999999.9999999-dev',
      'aliases' => 
      array (
      ),
      'reference' => '3e771c589dee414724be473c24ad16dae50c1960',
    ),
    'nette/di' => 
    array (
      'pretty_version' => 'v3.0.x-dev',
      'version' => '3.0.9999999.9999999-dev',
      'aliases' => 
      array (
      ),
      'reference' => '1a3210f0f1f971db8a6e970c716c1cebd28b7ab0',
    ),
    'nette/finder' => 
    array (
      'pretty_version' => 'v2.5.x-dev',
      'version' => '2.5.9999999.9999999-dev',
      'aliases' => 
      array (
      ),
      'reference' => '56c89ac03c793de7774f5dc105192834480580ba',
    ),
    'nette/http' => 
    array (
      'pretty_version' => 'v4.0.x-dev',
      'version' => '4.0.9999999.9999999-dev',
      'aliases' => 
      array (
      ),
      'reference' => 'ae82f9d5f1be916710bcaff6769465e977b68e5d',
    ),
    'nette/neon' => 
    array (
      'pretty_version' => 'v3.2.x-dev',
      'version' => '3.2.9999999.9999999-dev',
      'aliases' => 
      array (
      ),
      'reference' => 'e4ca6f4669121ca6876b1d048c612480e39a28d5',
    ),
    'nette/php-generator' => 
    array (
      'pretty_version' => 'v3.5.x-dev',
      'version' => '3.5.9999999.9999999-dev',
      'aliases' => 
      array (
      ),
      'reference' => '59bb35ed6e8da95854fbf7b7d47dce6156b42915',
    ),
    'nette/robot-loader' => 
    array (
      'pretty_version' => 'v3.4.x-dev',
      'version' => '3.4.9999999.9999999-dev',
      'aliases' => 
      array (
      ),
      'reference' => '3973cf3970d1de7b30888fd10b92dac9e0c2fd82',
    ),
    'nette/schema' => 
    array (
      'pretty_version' => 'v1.2.1',
      'version' => '1.2.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f5ed39fc96358f922cedfd1e516f0dadf5d2be0d',
    ),
    'nette/tester' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '2.4.x-dev',
      ),
      'reference' => '617683bfe73861e29bd503f81abc0ae7a9a72535',
    ),
    'nette/utils' => 
    array (
      'pretty_version' => 'v3.2.x-dev',
      'version' => '3.2.9999999.9999999-dev',
      'aliases' => 
      array (
      ),
      'reference' => '43fbb3419a6688d0bc21bfecb17f79a3e002ffc0',
    ),
    'phpstan/phpstan' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '0.12.x-dev',
      ),
      'reference' => 'a656ebf75c064b6eaf56562397dc0bc7c702bb81',
    ),
    'phpstan/phpstan-nette' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '0.12.x-dev',
      ),
      'reference' => 'c24d2bb6292b81cf3878b2892c49f9277e8789d0',
    ),
    'psr/http-client' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '1.0.x-dev',
      ),
      'reference' => '22b2ef5687f43679481615605d7a15c557ce85b1',
    ),
    'psr/http-client-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'psr/http-factory' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '1.0.x-dev',
      ),
      'reference' => '36fa03d50ff82abcae81860bdaf4ed9a1510c7cd',
    ),
    'psr/http-factory-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'psr/http-message' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '1.0.x-dev',
      ),
      'reference' => 'efd67d1dc14a7ef4fc4e518e7dee91c271d524e4',
    ),
    'psr/http-message-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'ralouphie/getallheaders' => 
    array (
      'pretty_version' => '3.0.3',
      'version' => '3.0.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '120b605dfeb996808c31b6477290a714d356e822',
    ),
    'tracy/tracy' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '2.8.x-dev',
      ),
      'reference' => 'a0c1b096dda355581eb99a0fa245e1a2f4c7a5fc',
    ),
  ),
);
private static $canGetVendors;
private static $installedByVendor = array();







public static function getInstalledPackages()
{
$packages = array();
foreach (self::getInstalled() as $installed) {
$packages[] = array_keys($installed['versions']);
}

if (1 === \count($packages)) {
return $packages[0];
}

return array_keys(array_flip(\call_user_func_array('array_merge', $packages)));
}









public static function isInstalled($packageName)
{
foreach (self::getInstalled() as $installed) {
if (isset($installed['versions'][$packageName])) {
return true;
}
}

return false;
}














public static function satisfies(VersionParser $parser, $packageName, $constraint)
{
$constraint = $parser->parseConstraints($constraint);
$provided = $parser->parseConstraints(self::getVersionRanges($packageName));

return $provided->matches($constraint);
}










public static function getVersionRanges($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

$ranges = array();
if (isset($installed['versions'][$packageName]['pretty_version'])) {
$ranges[] = $installed['versions'][$packageName]['pretty_version'];
}
if (array_key_exists('aliases', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['aliases']);
}
if (array_key_exists('replaced', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['replaced']);
}
if (array_key_exists('provided', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['provided']);
}

return implode(' || ', $ranges);
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['version'])) {
return null;
}

return $installed['versions'][$packageName]['version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getPrettyVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['pretty_version'])) {
return null;
}

return $installed['versions'][$packageName]['pretty_version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getReference($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['reference'])) {
return null;
}

return $installed['versions'][$packageName]['reference'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getRootPackage()
{
$installed = self::getInstalled();

return $installed[0]['root'];
}








public static function getRawData()
{
@trigger_error('getRawData only returns the first dataset loaded, which may not be what you expect. Use getAllRawData() instead which returns all datasets for all autoloaders present in the process.', E_USER_DEPRECATED);

return self::$installed;
}







public static function getAllRawData()
{
return self::getInstalled();
}



















public static function reload($data)
{
self::$installed = $data;
self::$installedByVendor = array();
}





private static function getInstalled()
{
if (null === self::$canGetVendors) {
self::$canGetVendors = method_exists('Composer\Autoload\ClassLoader', 'getRegisteredLoaders');
}

$installed = array();

if (self::$canGetVendors) {
foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
if (isset(self::$installedByVendor[$vendorDir])) {
$installed[] = self::$installedByVendor[$vendorDir];
} elseif (is_file($vendorDir.'/composer/installed.php')) {
$installed[] = self::$installedByVendor[$vendorDir] = require $vendorDir.'/composer/installed.php';
}
}
}

$installed[] = self::$installed;

return $installed;
}
}
