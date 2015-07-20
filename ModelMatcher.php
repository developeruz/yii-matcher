<?php

namespace developeruz\yii_matcher;

use yii\base\Model;
use yii\base\Security;
use yii\codeception\TestCase;
use yii\console\Exception;


class ModelMatcher extends TestCase
{

    public $class;

    public function __construct($class)
    {
        parent::__construct();
        $this->class = $class;
    }

    public function shouldBeSafe($attribute, $onScenario = Model::SCENARIO_DEFAULT)
    {
        $model = $this->getModel($onScenario);
        $this->assertTrue($model->isAttributeSafe($attribute));
    }

    public function shouldBeNotSafe($attribute, $onScenario = Model::SCENARIO_DEFAULT)
    {
        $model = $this->getModel($onScenario);
        $this->assertFalse($model->isAttributeSafe($attribute));
    }

    public function shouldBeRequired($attribute, $onScenario = Model::SCENARIO_DEFAULT)
    {
        $model = $this->getModel($onScenario);
        $this->assertTrue($model->isAttributeRequired($attribute));
    }

    public function shouldBeNotRequired($attribute, $onScenario = Model::SCENARIO_DEFAULT)
    {
        $model = $this->getModel($onScenario);
        $this->assertFalse($model->isAttributeRequired($attribute));
    }

    public function matchLength($attribute, $min = null, $max = null, $onScenario = Model::SCENARIO_DEFAULT)
    {
        $stringValidator = $this->getValidator('yii\validators\StringValidator', $attribute, $onScenario);
        $stringGenerator = new Security();

        if (!empty($min)) {
            $string = $stringGenerator->generateRandomString($min - 1);
            $this->assertFalse($stringValidator->validate($string));

            $string = $stringGenerator->generateRandomString($min);
            $this->assertTrue($stringValidator->validate($string));
        }

        if (!empty($max)) {
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

        if (!empty($links)) {
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

        if (!empty($links)) {
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

    private function getValidator($type, $attribute, $onScenario)
    {
        $model = $this->getModel($onScenario);
        $validators = $model->getActiveValidators($attribute);
        foreach ($validators as $v) {
            if ($v instanceof $type) {
                return $v;
            }
        }
        if (empty($validator)) {
            throw new Exception('Not found ' . $type . ' validator for this class');
        }
        return $validator;
    }
}