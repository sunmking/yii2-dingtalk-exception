<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Saviorlv\Log;

use yii\log\Target;
use yii\di\ServiceLocator;
use yii\log\LogRuntimeException;
use yii\base\InvalidConfigException;

/**
 * ```php
 * 'components' => [
 *     'log' => [
 *          'targets' => [
 *              [
 *                  'class' => 'Saviorlv\Log\ExceptionTarget',
 *                  'levels' => ['error', 'warning'],
 *                  'options' => [
 *                      'accessToken' => 'xxxxxxxxx',
 *                      'isAtAll' => false,
 *                      'atMobiles' => []
 *                  ],
 *              ],
 *          ],
 *     ],
 * ],
 * ```
 *
 */
class ExceptionTarget extends Target
{
    /**
     * @var array
     */
    public $options = [];
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if (empty($this->options['accessToken'])) {
            throw new InvalidConfigException('The "accessToken" option must be set.');
        }
    }

    /**
     * Sends log messages to dingtalk.
     * Starting from version 2.0.14, this method throws LogRuntimeException in case the log can not be exported.
     * @return mixed
     * @throws InvalidConfigException
     * @throws LogRuntimeException
     */
    public function export()
    {
        $messages = array_map([$this, 'formatMessage'], $this->messages);

        $response = $this->sendMsg($messages);
        if ($response['errmsg']!=='ok') {
            throw new LogRuntimeException('Unable to export log through DingTalk!');
        }else{
            return $response;
        }
    }

    /**
     * @param $message
     * @return mixed
     * @throws InvalidConfigException
     */
    public function sendMsg($message){
        $locator = new ServiceLocator;
        $locator->setComponents([
            'robot' => [
                'class' => 'Saviorlv\Dingtalk\Robot',
                'accessToken' => $this->options['accessToken']
            ],
        ]);
        $robot = $locator->get('robot');
        $response = $robot->sendTextMsg($message,$this->options['atMobiles'],$this->options['isAtAll']);
        return json_decode($response,true);
    }
}