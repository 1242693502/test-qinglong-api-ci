<?php

namespace QingLong\Validator;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\Arr;

class Rules
{
    /**
     * 整型规则
     *
     * @var array
     */
    protected $intRules = [
        'tinyint'   => [
            'signed'   => ['min' => -128, 'max' => 127],
            'unsigned' => ['min' => 0, 'max' => 255],
        ],
        'smallint'  => [
            'signed'   => ['min' => -32768, 'max' => 32767],
            'unsigned' => ['min' => 0, 'max' => 65535],
        ],
        'mediumint' => [
            'signed'   => ['min' => -8388608, 'max' => 8388607],
            'unsigned' => ['min' => 0, 'max' => 16777215],
        ],
        'int'       => [
            'signed'   => ['min' => -2147483648, 'max' => 2147483647],
            'unsigned' => ['min' => 0, 'max' => 4294967295],
        ],
        'bigint'    => [
            // 可能会超过php int最大值，所以用字符串存储
            'signed'   => ['min' => '-9223372036854775808', 'max' => '9223372036854775807'],
            'unsigned' => ['min' => 0, 'max' => '18446744073709551615'],
        ],
    ];

    /**
     * 验证车牌号码是否合法
     *
     * 规则用法：
     *     车辆车牌 'truck_plate_number'   => 'required|license_plate_number'
     *     挂车车牌 'trailer_plate_number' => 'required|license_plate_number:trailer'
     *
     * @param string                           $attribute
     * @param mixed                            $value
     * @param array                            $parameters
     * @param \Illuminate\Validation\Validator $validator
     *
     * @return bool
     */
    public function ruleLicensePlateNumber($attribute, $value, $parameters, $validator): bool
    {
        $middlePattern = in_array('trailer', $parameters) ? '{4}挂' : '{5,6}';
        $pattern       = '/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新][A-Z][0-9A-Z]' . $middlePattern . '$/u';
        return preg_match($pattern, strtoupper($value));
    }

    /**
     * 验证车型编码是否存在
     *
     * @param string                           $attribute
     * @param mixed                            $value
     * @param array                            $parameters
     * @param \Illuminate\Validation\Validator $validator
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function ruleTruckTypeCode($attribute, $value, $parameters, $validator): bool
    {
        return app('file-db')->load('truck.types')->where('code', $value)->isNotEmpty();
    }

    /**
     * 验证车长编码是否存在
     *
     * @param string                           $attribute
     * @param mixed                            $value
     * @param array                            $parameters
     * @param \Illuminate\Validation\Validator $validator
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function ruleTruckLengthCode($attribute, $value, $parameters, $validator): bool
    {
        return app('file-db')->load('truck.lengths')->where('code', $value)->isNotEmpty();
    }

    /**
     * 判断该值是否图片code
     *
     * @param string                           $attribute
     * @param mixed                            $value
     * @param array                            $parameters
     * @param \Illuminate\Validation\Validator $validator
     *
     * @return bool
     */
    public function ruleImageCode($attribute, $value, $parameters, $validator): bool
    {
        return strlen($value) === 32;
    }

    /**
     * 验证手机号码规则
     *
     * @param string                           $attribute
     * @param mixed                            $value
     * @param array                            $parameters
     * @param \Illuminate\Validation\Validator $validator
     *
     * @return bool
     */
    public function rulePhoneNumber($attribute, $value, $parameters, $validator): bool
    {
        return preg_match('/^1[0-9]{10}$/', $value) === 1;
    }

    /**
     * 整型范围限制
     *
     * @param string                           $attribute
     * @param mixed                            $value
     * @param array                            $parameters
     * @param \Illuminate\Validation\Validator $validator
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    public function ruleQlInt($attribute, $value, $parameters, $validator)
    {
        // 判断是否整型
        if (!$validator->validateInteger($attribute, $value)) {
            return false;
        }

        // 判断范围
        $intType = Arr::get($parameters, 0, '') . 'int';
        $rules   = Arr::get($this->intRules, $intType);

        if (empty($rules)) {
            throw new \InvalidArgumentException("Int rule $intType does not exists.");
        }

        $isSigned   = (bool)Arr::get($parameters, 1, true);
        $signedType = $isSigned ? 'signed' : 'unsigned';
        $min        = $rules[$signedType]['min'];
        $max        = $rules[$signedType]['max'];

        return $min <= $value && $value <= $max;
    }

    /**
     * 将扩展规则添加到validator中
     *
     * @param \Illuminate\Contracts\Validation\Factory $factory
     */
    public static function addTo(Factory $factory)
    {
        $className = static::class;
        foreach (get_class_methods($className) as $method) {
            if (strpos($method, 'rule') === 0) {
                $factory->extend(substr($method, 4), $className . '@' . $method);
            } elseif (strpos($method, 'implicitRule') === 0) {
                $factory->extendImplicit(substr($method, 12), $className . '@' . $method);
            }
        }
    }

    /**
     * Require a certain number of parameters to be present.
     *
     * @param  int    $count
     * @param  array  $parameters
     * @param  string $rule
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function requireParameterCount($count, $parameters, $rule)
    {
        if (count($parameters) < $count) {
            throw new \InvalidArgumentException("Validation rule $rule requires at least $count parameters.");
        }
    }
}