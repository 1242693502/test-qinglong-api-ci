<?php

namespace App\Http\Requests\InternalApi;

use App\Http\Requests\Request;
use Carbon\Carbon;

class BaseRequest extends Request
{
    /**
     * @var string 通用时间格式
     */
    protected $dateFormat = Carbon::RFC3339;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * 通用状态描述
     *
     * @return array
     */
    protected function commonAttributes()
    {
        return array_merge(parent::commonAttributes(), [
        ]);
    }

    /**
     * Get the validated data from the request.
     *
     * @return array
     */
    public function validated()
    {
        $rules = $this->container->call([$this, 'rules']);

        $dateTimeKeys  = [];
        $validatedKeys = [];
        foreach ($rules as $key => $rule) {
            $realKey = explode('.', $key)[0];
            if ($this->hasDateTimeRule($rule)) {
                $dateTimeKeys[] = $realKey;
            } else {
                $validatedKeys[] = $realKey;
            }
        }

        // 取出对应数据
        $validated = $this->only($validatedKeys);
        try {
            foreach ($dateTimeKeys as $key) {
                $validated[$key] = Carbon::createFromFormat($this->dateFormat, $this->input($key));
            }
        } catch (\Exception $e) {
        }

        return $validated;
    }

    /**
     * 检查规则是否存在时间格式
     *
     * @param string|array $rule
     *
     * @return bool
     */
    private function hasDateTimeRule($rule)
    {
        $dateTimeRule = 'date_format:' . $this->dateFormat;
        foreach ((array)$rule as $singleRule) {
            if (is_string($singleRule) && $singleRule !== '' && mb_strpos($singleRule, $dateTimeRule) !== false) {
                return true;
            }
        }

        return false;
    }
}
