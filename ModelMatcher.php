<?php

namespace developeruz\yii_matcher;

use yii\base\Model;
use yii\base\Security;
use yii\codeception\TestCase;
use yii\console\Exception;
use yii\validators\Validator;


class ModelMatcher extends TestCase {

    public $class;

    public function __construct($class)
    {
        parent::__construct();
        $this->class = $class;
    }

    public function mustBeSafe($attribute, $onScenario = Model::SCENARIO_DEFAULT)
    {
        $model = $this->getModel($onScenario);
        $this->assertTrue($model->isAttributeSafe($attribute));
    }

    public function mustBeNotSafe($attribute, $onScenario = Model::SCENARIO_DEFAULT)
    {
        $model = $this->getModel($onScenario);
        $this->assertFalse($model->isAttributeSafe($attribute));
    }

    public function mustBeRequired($attribute, $onScenario = Model::SCENARIO_DEFAULT)
    {
        $model = $this->getModel($onScenario);
        $this->assertTrue($model->isAttributeRequired($attribute));
    }

    public function mustBeNotRequired($attribute, $onScenario = Model::SCENARIO_DEFAULT)
    {
        $model = $this->getModel($onScenario);
        $this->assertFalse($model->isAttributeRequired($attribute));
    }

    public function matchLength($attribute, $min = null, $max = null, $onScenario = Model::SCENARIO_DEFAULT)
    {
        $stringValidator = $this->getValidator('yii\validators\StringValidator', ['max' => $max, 'min' => $min], $attribute, $onScenario);
        $stringGenerator = new Security();

        if(!empty($min))
        {
            $string = $stringGenerator->generateRandomString($min - 1);
            $this->assertFalse($stringValidator->validate($string));

            $string = $stringGenerator->generateRandomString($min);
            $this->assertTrue($stringValidator->validate($string));
        }

        if(!empty($max))
        {
            $string = $stringGenerator->generateRandomString($max + 1);
            $this->assertFalse($stringValidator->validate($string));

            $string = $stringGenerator->generateRandomString($max);
            $this->assertTrue($stringValidator->validate($string));
        }
    }

    public function hasOne($relationName, $relatedClass, $links = null)
    {
        $model = $this->getModel();
        $relation = $model->getRelation($relationName);

        $this->assertEquals($relatedClass, $relation->modelClass);
        $this->assertFalse($relation->multiple);

        if(!empty($links))
        {
            $actualLinks = $relation->link;
            $this->assertEmpty(array_diff($actualLinks, $links));
        }
    }

    public function hasMany($relationName, $relatedClass, $links = null)
    {
        $model = $this->getModel();
        $relation = $model->getRelation($relationName);

        $this->assertEquals($relatedClass, $relation->modelClass);
        $this->assertTrue($relation->multiple);

        if(!empty($links))
        {
            $actualLinks = $relation->link;
            $this->assertEmpty(array_diff($actualLinks, $links));
        }
    }


    private function getModel($scenario = Model::SCENARIO_DEFAULT)
    {
        $model = new $this->class();
        $model->scenario = $scenario;
        return $model;
    }

    private function getValidator($type, $params, $attribute , $onScenario)
    {
        $model = $this->getModel($onScenario);
        $validator = Validator::createValidator($type, $model, $attribute, $params);
        if(empty($validator))
        {
            throw new Exception('Can not generate validator');
        }
        return $validator;
    }
}