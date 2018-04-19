<?php

/*
 * This file is part of PHPacto
 *
 * Copyright (c) 2017  Damian Długosz <bigfootdd@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

/*
 * PHPacto - Contract testing solution
 *
 * Copyright (c) 2017  Damian Długosz
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Bigfoot\PHPacto\Matcher\Rules;

use Bigfoot\PHPacto\Matcher\Mismatches;

class StringEqualsRuleTest extends RuleAbstractTest
{
    public function test_it_is_normalizable()
    {
        $rule = new StringEqualsRule('');

        $expected = [
            '@rule' => StringEqualsRule::class,
            'caseSensitive' => false,
            'sample' => '',
        ];

        self::assertEquals($expected, $this->normalizer->normalize($rule));
    }

    public function test_it_is_denormalizable()
    {
        $data = [
            '@rule' => StringEqualsRule::class,
            'caseSensitive' => false,
            'sample' => '',
        ];

        $rule = $this->normalizer->denormalize($data, Rule::class);

        self::assertInstanceOf(StringEqualsRule::class, $rule);
        self::assertSame('', $rule->getSample());
        self::assertFalse($rule->isCaseSensitive());
    }

    public function test_normalize_string_equals_case_sensitive()
    {
        $rule = new StringEqualsRule('S', true);

        $data = $this->normalizer->normalize($rule);

        self::assertSame('S', $data);
    }

    public function test_denormalize_string_equals_case_sensitive()
    {
        $data = 'S';

        $rule = $this->normalizer->denormalize($data, Rule::class);

        self::assertInstanceOf(StringEqualsRule::class, $rule);
        self::assertSame('S', $rule->getSample());
        self::assertTrue($rule->isCaseSensitive());
    }

    public function matchesTrueProvider()
    {
        return [
            [true, '', '', true],
            [true, '', '', false],
            [true, 'a', 'a', true],
            [true, 'a', 'A', false],
        ];
    }

    public function matchesFalseProvider()
    {
        return [
            [false, 'a', 'A', true],
            [false, 'a', 'b', true],
            [false, 'a', 'b', false],
            [false, 'a', '', true],
            [false, '', 'b', true],
        ];
    }

    /**
     * @dataProvider matchesTrueProvider
     * @dataProvider matchesFalseProvider
     *
     * @param mixed $ruleValue
     * @param mixed $testValue
     */
    public function testMatch(bool $shouldMatch, $ruleValue, $testValue, bool $caseSensitive)
    {
        $rule = new StringEqualsRule($ruleValue, $caseSensitive);

        if (!$shouldMatch) {
            $this->expectException(Mismatches\ValueMismatch::class);
        }

        $rule->assertMatch($testValue);

        self::assertTrue(true, 'No exceptions should be thrown if matching');
    }
}
