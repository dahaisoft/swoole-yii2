<?php

namespace tourze\swoole\yii2;

use ReflectionClass;
use Yii;
use yii\base\Object;

class Container extends \yii\di\Container
{

    /**
     * @var array 类的别名
     */
    public static $classAlias = [
        'yii\db\Connection' => 'tourze\swoole\yii2\db\Connection',
        'yii\debug\Module' => 'tourze\swoole\yii2\debug\Module',
        'yii\debug\panels\ConfigPanel' => 'tourze\swoole\yii2\debug\ConfigPanel',
        'yii\debug\panels\RequestPanel' => 'tourze\swoole\yii2\debug\RequestPanel',
        'yii\log\Dispatcher' => 'tourze\swoole\yii2\log\Dispatcher',
        'yii\log\FileTarget' => 'tourze\swoole\yii2\log\FileTarget',
        'yii\swiftmailer\Mailer' => 'tourze\swoole\yii2\mailer\SwiftMailer',
        'yii\web\Request' => 'tourze\swoole\yii2\web\Request',
        'yii\web\Response' => 'tourze\swoole\yii2\web\Response',
        'yii\web\Session' => 'tourze\swoole\yii2\web\Session',
        'yii\web\AssetManager' => 'tourze\swoole\yii2\web\AssetManager',
        'yii\web\ErrorHandler' => 'tourze\swoole\yii2\web\ErrorHandler',
        'yii\web\User' => 'tourze\swoole\yii2\web\User',
        'yii\web\View' => 'tourze\swoole\yii2\web\View',
    ];

    /**
     * @var array 需要持久化的类
     */
    public static $persistClasses = [
        'yii\behaviors\TimestampBehavior',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\ButtonDropdown',
        'yii\bootstrap\Modal',
        'yii\caching\ApcCache',
        'yii\caching\ArrayCache',
        'yii\caching\FileCache',
        'yii\captcha\Captcha',
        'yii\captcha\CaptchaAsset',
        'yii\captcha\CaptchaValidator',
        'yii\data\Pagination',
        'yii\data\Sort',
        'yii\db\ColumnSchema',
        'yii\db\mysql\Schema',
        'yii\filters\AccessControl',
        'yii\filters\AccessRule',
        'yii\filters\ContentNegotiator',
        'yii\filters\Cors',
        'yii\filters\VerbFilter',
        'yii\grid\ActionColumn',
        'yii\grid\CheckboxColumn',
        'yii\grid\DataColumn',
        'yii\grid\GridView',
        'yii\grid\GridViewAsset',
        'yii\grid\SerialColumn',
        'yii\i18n\Formatter',
        'yii\i18n\I18N',
        'yii\i18n\PhpMessageSource',
        'yii\rbac\Assignment',
        'yii\rbac\Item',
        'yii\rbac\Permission',
        'yii\rbac\Role',
        'yii\rest\Serializer',
        'yii\rest\UrlRule',
        'yii\validators\BooleanValidator',
        'yii\validators\CompareValidator',
        'yii\validators\DateValidator',
        'yii\validators\DefaultValueValidator',
        'yii\validators\EachValidator',
        'yii\validators\EmailValidator',
        'yii\validators\ExistValidator',
        'yii\validators\FileValidator',
        'yii\validators\FilterValidator',
        'yii\validators\ImageValidator',
        'yii\validators\InlineValidator',
        'yii\validators\IpValidator',
        'yii\validators\NumberValidator',
        'yii\validators\RangeValidator',
        'yii\validators\RegularExpressionValidator',
        'yii\validators\RequiredValidator',
        'yii\validators\SafeValidator',
        'yii\validators\StringValidator',
        'yii\validators\UniqueValidator',
        'yii\validators\UrlValidator',
        'yii\web\AssetConverter',
        'yii\web\Cookie',
        'yii\web\GroupUrlRule',
        'yii\web\HeaderCollection',
        'yii\web\HtmlResponseFormatter',
        'yii\web\JqueryAsset',
        'yii\web\JsonParser',
        'yii\web\Link',
        'yii\web\MultipartFormDataParser',
        'yii\web\UrlNormalizer',
        'yii\web\UrlRule',
        'yii\web\UserEvent',
        'yii\web\XmlResponseFormatter',
        'yii\web\YiiAsset',
        'yii\widgets\ActiveField',
        'yii\widgets\ActiveFormAsset',
        'yii\widgets\Breadcrumbs',
        'yii\widgets\InputWidget',
        'yii\widgets\LinkPager',
        'yii\widgets\Pjax',
    ];

    /**
     * @var array 持久化的类实例
     */
    public static $persistInstances = [];

    /**
     * 在最终构造类时, 尝试检查类的别名
     *
     * @inheritdoc
     */
    protected function build($class, $params, $config)
    {
        // 检查类的别名
        if (isset(self::$classAlias[$class]))
        {
            $class = self::$classAlias[$class];
            //echo "alias: $class\n";
        }

        // 构造方法参数为空才走这个流程
        if ($class && empty($params) && in_array($class, self::$persistClasses))
        {
            if ( ! isset(self::$persistInstances[$class]))
            {
                /* @var $reflection ReflectionClass */
                list ($reflection, $dependencies) = $this->getDependencies($class);
                self::$persistInstances[$class] = $reflection->newInstanceWithoutConstructor();
            }
            $object = clone self::$persistInstances[$class];
            Yii::configure($object, $config);
            if ($object instanceof Object)
            {
                $object->init();
            }
            return $object;
        }
        //echo "build: ".json_encode(func_get_args())."\n";

        return parent::build($class, $params, $config);
    }
}
