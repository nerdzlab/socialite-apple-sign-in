<?php

namespace Nerdzlab\SocialiteAppleSignIn\Tests;

class DataStub
{
    public static function token(): string
    {
        return 'eyJraWQiOiI4NkQ4OEtmIiwiYWxnIjoiUlMyNTYifQ.eyJpc3MiOiJodHRwczovL2FwcGxlaWQuYXBwbGUuY29tIiwiYXVkIjoiTmVyZHpsYWIuQXJhdHJhaW4tZGV2ZWxvcG1lbnQiLCJleHAiOjE1ODgyNTgyODQsImlhdCI6MTU4ODI1NzY4NCwic3ViIjoiMDAwMDUwLmE2YzVlNTYwNmMyMDQ5NDFiMTAzZGI0MmJiNWFmYjQwLjEzMzIiLCJjX2hhc2giOiJ3bVc0OUZZaVdTQnpzQ19UazNRalB3IiwiZW1haWwiOiJpZ29yMTk5NG1ha2FyYUBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6InRydWUiLCJhdXRoX3RpbWUiOjE1ODgyNTc2ODQsIm5vbmNlX3N1cHBvcnRlZCI6dHJ1ZX0.Dh2rB9jsJ1UaNYn11F5XXvhQemCsWqDGPWh9XAQUFk5kQ5A2u8bHfwIU0D4w-J6Bk9mzGJsPKCNEFsrzVylubBT5wV966Teq43q1fb0WusFQhlhaFDiLBAHXxvQcHda7Hr5GE78x2Gbc0MXq_-Y9RuWeH_RPbxA2Nt0BsE6q3EjcwZHUhWbsUtmcfgHDNxNi_yLHM4YTLdpDcacqU-IWgidYFknHFeqjPBS-FzebRHyyp5fRNvYanpdtyeKQEId0_Ei4LT9jA7SKF88Q3sshTIUWrgadq-JANPSxOk7-d2ch_W2lXk4jq4LXowvPl2gbJjppAUsS0ZcD1vdAb2FRHA';
    }

    public static function tokenBody(): string
    {
        return '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiGaLqP6y+SJCCBq5Hv6p
GDbG/SQ11MNjH7rWHcCFYz4hGwHC4lcSurTlV8u3avoVNM8jXevG1Iu1SY11qInq
UvjJur++hghr1b56OPJu6H1iKulSxGjEIyDP6c5BdE1uwprYyr4IO9th8fOwCPyg
jLFrh44XEGbDIFeImwvBAGOhmMB2AD1n1KviyNsH0bEB7phQtiLk+ILjv1bORSRl
8AK677+1T8isGfHKXGZ/ZGtStDe7Lu0Ihp8zoUt59kx2o9uWpROkzF56ypresiIl
4WprClRCjz8x6cPZXU2qNWhu71TQvUFwvIvbkE1oYaJMb0jcOTmBRZA2QuYw+zHL
wQIDAQAB
-----END PUBLIC KEY-----
';
    }

    public static function tokenWithInvalidHeader(): string
    {
        return '123.eyJpc3MiOiJodHRwczovL2FwcGxlaWQuYXBwbGUuY29tIiwiYXVkIjoiTmVyZHpsYWIuQXJhdHJhaW4tZGV2ZWxvcG1lbnQiLCJleHAiOjE1ODgyNTgyODQsImlhdCI6MTU4ODI1NzY4NCwic3ViIjoiMDAwMDUwLmE2YzVlNTYwNmMyMDQ5NDFiMTAzZGI0MmJiNWFmYjQwLjEzMzIiLCJjX2hhc2giOiJ3bVc0OUZZaVdTQnpzQ19UazNRalB3IiwiZW1haWwiOiJpZ29yMTk5NG1ha2FyYUBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6InRydWUiLCJhdXRoX3RpbWUiOjE1ODgyNTc2ODQsIm5vbmNlX3N1cHBvcnRlZCI6dHJ1ZX0.Dh2rB9jsJ1UaNYn11F5XXvhQemCsWqDGPWh9XAQUFk5kQ5A2u8bHfwIU0D4w-J6Bk9mzGJsPKCNEFsrzVylubBT5wV966Teq43q1fb0WusFQhlhaFDiLBAHXxvQcHda7Hr5GE78x2Gbc0MXq_-Y9RuWeH_RPbxA2Nt0BsE6q3EjcwZHUhWbsUtmcfgHDNxNi_yLHM4YTLdpDcacqU-IWgidYFknHFeqjPBS-FzebRHyyp5fRNvYanpdtyeKQEId0_Ei4LT9jA7SKF88Q3sshTIUWrgadq-JANPSxOk7-d2ch_W2lXk4jq4LXowvPl2gbJjppAUsS0ZcD1vdAb2FRHA';
    }

    public static function tokenWithInvalidKid(): string
    {
        return 'eyJraWQiOiIxMjM0NSIsImFsZyI6IlJTMjU2In0=.eyJpc3MiOiJodHRwczovL2FwcGxlaWQuYXBwbGUuY29tIiwiYXVkIjoiTmVyZHpsYWIuQXJhdHJhaW4tZGV2ZWxvcG1lbnQiLCJleHAiOjE1ODgyNTgyODQsImlhdCI6MTU4ODI1NzY4NCwic3ViIjoiMDAwMDUwLmE2YzVlNTYwNmMyMDQ5NDFiMTAzZGI0MmJiNWFmYjQwLjEzMzIiLCJjX2hhc2giOiJ3bVc0OUZZaVdTQnpzQ19UazNRalB3IiwiZW1haWwiOiJpZ29yMTk5NG1ha2FyYUBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6InRydWUiLCJhdXRoX3RpbWUiOjE1ODgyNTc2ODQsIm5vbmNlX3N1cHBvcnRlZCI6dHJ1ZX0.Dh2rB9jsJ1UaNYn11F5XXvhQemCsWqDGPWh9XAQUFk5kQ5A2u8bHfwIU0D4w-J6Bk9mzGJsPKCNEFsrzVylubBT5wV966Teq43q1fb0WusFQhlhaFDiLBAHXxvQcHda7Hr5GE78x2Gbc0MXq_-Y9RuWeH_RPbxA2Nt0BsE6q3EjcwZHUhWbsUtmcfgHDNxNi_yLHM4YTLdpDcacqU-IWgidYFknHFeqjPBS-FzebRHyyp5fRNvYanpdtyeKQEId0_Ei4LT9jA7SKF88Q3sshTIUWrgadq-JANPSxOk7-d2ch_W2lXk4jq4LXowvPl2gbJjppAUsS0ZcD1vdAb2FRHA';
    }

    public static function tokenWithFakeHeader(): string
    {
        return 'eyJxd2VydHkiOjF9.eyJpc3MiOiJodHRwczovL2FwcGxlaWQuYXBwbGUuY29tIiwiYXVkIjoiTmVyZHpsYWIuQXJhdHJhaW4tZGV2ZWxvcG1lbnQiLCJleHAiOjE1ODgyNTgyODQsImlhdCI6MTU4ODI1NzY4NCwic3ViIjoiMDAwMDUwLmE2YzVlNTYwNmMyMDQ5NDFiMTAzZGI0MmJiNWFmYjQwLjEzMzIiLCJjX2hhc2giOiJ3bVc0OUZZaVdTQnpzQ19UazNRalB3IiwiZW1haWwiOiJpZ29yMTk5NG1ha2FyYUBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6InRydWUiLCJhdXRoX3RpbWUiOjE1ODgyNTc2ODQsIm5vbmNlX3N1cHBvcnRlZCI6dHJ1ZX0.Dh2rB9jsJ1UaNYn11F5XXvhQemCsWqDGPWh9XAQUFk5kQ5A2u8bHfwIU0D4w-J6Bk9mzGJsPKCNEFsrzVylubBT5wV966Teq43q1fb0WusFQhlhaFDiLBAHXxvQcHda7Hr5GE78x2Gbc0MXq_-Y9RuWeH_RPbxA2Nt0BsE6q3EjcwZHUhWbsUtmcfgHDNxNi_yLHM4YTLdpDcacqU-IWgidYFknHFeqjPBS-FzebRHyyp5fRNvYanpdtyeKQEId0_Ei4LT9jA7SKF88Q3sshTIUWrgadq-JANPSxOk7-d2ch_W2lXk4jq4LXowvPl2gbJjppAUsS0ZcD1vdAb2FRHA';
    }

    public static function tokenValidTime(): int
    {
        return 1588258184;
    }

    public static function clientId(): string
    {
        return 'Nerdzlab.Aratrain-development';
    }

    public static function JWKResponseBody(): string
    {
        return '{"keys": [{"kty": "RSA","kid": "86D88Kf","use": "sig","alg": "RS256","n": "iGaLqP6y-SJCCBq5Hv6pGDbG_SQ11MNjH7rWHcCFYz4hGwHC4lcSurTlV8u3avoVNM8jXevG1Iu1SY11qInqUvjJur--hghr1b56OPJu6H1iKulSxGjEIyDP6c5BdE1uwprYyr4IO9th8fOwCPygjLFrh44XEGbDIFeImwvBAGOhmMB2AD1n1KviyNsH0bEB7phQtiLk-ILjv1bORSRl8AK677-1T8isGfHKXGZ_ZGtStDe7Lu0Ihp8zoUt59kx2o9uWpROkzF56ypresiIl4WprClRCjz8x6cPZXU2qNWhu71TQvUFwvIvbkE1oYaJMb0jcOTmBRZA2QuYw-zHLwQ","e": "AQAB"}]}';
    }

    public static function kid(): string
    {
        return '86D88Kf';
    }

    public static function userId(): string
    {
        return '000050.a6c5e5606c204941b103db42bb5afb40.1332';
    }
}