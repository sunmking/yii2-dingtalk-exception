<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Saviorlv\Log;

use Yii;
use yii\base\InvalidConfigException;
use yii\log\Logger;
use yii\log\Target;
use yii\di\ServiceLocator;

/**
 * ```php
 * 'components' => [
 *     'log' => [
 *          'targets' => [
 *              [
 *                  'class' => 'Saviorlv\Log\ExceptionTarget',
 *                  'levels' => ['error', 'warning'],
 *                  'message' => [
 *                      'accessToken' => 'xxxxxxxxx',
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
    public $message = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if (empty($this->message['accessToken'])) {
            throw new InvalidConfigException('The "accessToken" option must be set.');
        }
    }

    /**
     * Sends log messages to dingtalk.
     * Starting from version 2.0.14, this method throws LogRuntimeException in case the log can not be exported.
     * @throws LogRuntimeException
     */
    public function export()
    {
        $messages = array_map([$this, 'formatMessage'], $this->messages);
        
        $dingTalk = $this->sendMsg($messages);

        if ($dingTalk['errmsg']!=='ok') {
            throw new LogRuntimeException('Unable to export log through DingTalk!');
        }else{
            Yii::trace(json_encode($dingTalk),Logger::LEVEL_INFO);
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
                'accessToken' => $this->message['accessToken']
            ],
        ]);

        $robot = $locator->get('robot');

        $response = $robot->sendMarkdownMsg('ww',$message);

        return json_decode($response,true);
    }
}
