<?php

namespace Orm\Core;

abstract class Inflect {
  private const _PLURAL = [
    '/(quiz)$/i'               => "$1zes",
    '/^(ox)$/i'                => "$1en",
    '/([m|l])ouse$/i'          => "$1ice",
    '/(matr|vert|ind)ix|ex$/i' => "$1ices",
    '/(x|ch|ss|sh)$/i'         => "$1es",
    '/([^aeiouy]|qu)y$/i'      => "$1ies",
    '/(hive)$/i'               => "$1s",
    '/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
    '/(shea|lea|loa|thie)f$/i' => "$1ves",
    '/sis$/i'                  => "ses",
    '/([ti])um$/i'             => "$1a",
    '/(tomat|potat|ech|her|vet)o$/i' => "$1oes",
    '/(bu)s$/i'                => "$1ses",
    '/(alias)$/i'              => "$1es",
    '/(octop)us$/i'            => "$1i",
    '/(ax|test)is$/i'          => "$1es",
    '/(us)$/i'                 => "$1es",
    '/s$/i'                    => "s",
    '/$/'                      => "s"
  ];
  private const _SINGULAR = [
    '/(quiz)zes$/i'             => "$1",
    '/(matr)ices$/i'            => "$1ix",
    '/(vert|ind)ices$/i'        => "$1ex",
    '/^(ox)en$/i'               => "$1",
    '/(alias)es$/i'             => "$1",
    '/(octop|vir)i$/i'          => "$1us",
    '/(cris|ax|test)es$/i'      => "$1is",
    '/(shoe)s$/i'               => "$1",
    '/(o)es$/i'                 => "$1",
    '/(bus)es$/i'               => "$1",
    '/([m|l])ice$/i'            => "$1ouse",
    '/(x|ch|ss|sh)es$/i'        => "$1",
    '/(m)ovies$/i'              => "$1ovie",
    '/(s)eries$/i'              => "$1eries",
    '/([^aeiouy]|qu)ies$/i'     => "$1y",
    '/([lr])ves$/i'             => "$1f",
    '/(tive)s$/i'               => "$1",
    '/(hive)s$/i'               => "$1",
    '/(li|wi|kni)ves$/i'        => "$1fe",
    '/(shea|loa|lea|thie)ves$/i' => "$1f",
    '/(^analy)ses$/i'           => "$1sis",
    '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i'  => "$1$2sis",
    '/([ti])a$/i'               => "$1um",
    '/(n)ews$/i'                => "$1ews",
    '/(h|bl)ouses$/i'           => "$1ouse",
    '/(corpse)s$/i'             => "$1",
    '/(us)es$/i'                => "$1",
    '/s$/i'                     => ""
  ];
  private const _IRREGULAR = [
    'move'   => 'moves',
    'foot'   => 'feet',
    'goose'  => 'geese',
    'sex'    => 'sexes',
    'child'  => 'children',
    'man'    => 'men',
    'tooth'  => 'teeth',
    'person' => 'people'
  ];

  private const _UNCOUNTABLE = ['sheep', 'fish', 'deer', 'series', 'species', 'money', 'rice', 'information', 'equipment'];

  public static function pluralize(string $string): string {
    if (in_array(strtolower($string), self::_UNCOUNTABLE)) {
      return $string;
    }

    foreach (self::_IRREGULAR as $pattern => $result) {
      $pattern = '/' . $pattern . '$/i';

      if (preg_match($pattern, $string)) {
        return preg_replace($pattern, $result, $string);
      }
    }

    foreach (self::_PLURAL as $pattern => $result) {
      if (preg_match($pattern, $string)) {
        return preg_replace($pattern, $result, $string);
      }
    }

    return $string;
  }
  public static function singularize(string $string): string {
    if (in_array(strtolower($string), self::_UNCOUNTABLE)) {
      return $string;
    }

    foreach (self::_IRREGULAR as $result => $pattern) {
      $pattern = '/' . $pattern . '$/i';

      if (preg_match($pattern, $string)) {
        return preg_replace($pattern, $result, $string);
      }
    }

    foreach (self::_SINGULAR as $pattern => $result) {
      if (preg_match($pattern, $string)) {
        return preg_replace($pattern, $result, $string);
      }
    }

    return $string;
  }
  public static function pluralizeIf(int $count, string $string): string {
    if ($count == 1) {
      return '1 ' . $string;
    }
    return $count . ' ' . self::pluralize($string);
  }
}
