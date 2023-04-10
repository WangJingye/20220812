<?php
	return [
        'weibo' => [
            'client_id'     => 'your-app-id',
            'client_secret' => 'your-app-secret',
            'redirect'      => 'http://localhost/socialite/callback.php',
        ],

//        'wechat' => [
//            'client_id'     => 'wxa793b4b07022d88b',
//            'client_secret' => '7f70f34acf535d7ea10c0c159bc3952f',
//            'redirect'      => 'https://apiprod.dlc.com.cn/member/sso/wechat',
//        ],
        'wechat' => [
            'client_id' => 'wxd94572ea7e704187',
            'client_secret' => 'f5731703ad5711c6057bc8e23645d6c6',
            'redirect' => 'https://www.dlc.com.cn/member/sso/wechat',
]       ,

        //dora_test
//        'alipay' => [
//            'client_id'     => '2021001189661500',
//            'client_secret' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArSM6kVZw71qakN+HWXbd4E76mp767pC/gldtfsFxNjgC2cCy1JU3X1AjfujdrNaDn+OB/nj6UiYbu/NiSEyen+DG0Y6S0zyKX1TdFjCEEBw4n2JjBLshXeu2pgXTC0yOSR5z4vbCHlFNggyD9cmX/4K1X7XMGNHaSNvOPeopBCq/DQDNS8yNvNY8hsSHqlyMyZaValWyw7xRpz1x8ZTi67JE7X53hhg5LOgY+BbK3tl15dIRCVUNytZFoAfHPW9PzCyfqHcL5rOrxWWLQYYQHMxJR9EZAQMmYaTD5O8GoF4bl/mmMGJpfJ4sUwpn/QSRgByxETwf4jAeDRB+nrta4wIDAQAB',
//            'redirect'      => 'https://apiprod.dlc.com.cn/member/sso/alipay',
//            'secret_key' => 'MIIEpAIBAAKCAQEAp+7uBpUTvi4uXMDvnnq09x4pkK0nR1rAaO1C0oQmQfXEFv3I2sBvks7anyrv3MHZXMwNYJq7JXfGPVq8PoeMUt6xBqvfI0M3UN49DctsXVuc0bwJcM3hvr4OEANldQ12Gkv9vvte/Y0fsvksOXsYuDMErFjFAitMnTIRDr4rumIWjr6vbIl69rQzydbSH6O5yD69mS1umeilt6wUpYmecEKLRMzRbZ7wmtQr8x8GNePy5POdwh7GDWG64lril4Blxfv+TYEtIoTuYWuTGt5X54C8VPnKcyCp1hyqOg2ZNR9ct1vqxjwRYwbei55aaIIV/4tyB/q0zd2tD6vM2l1S8QIDAQABAoIBADQvBd4bwxfM5/FArvUzMjq8L7RaaiM9Kx8v4xzhnbXPdhCi6iA3vjQDfWIKiKCzyCSS7/E+A7uf3YVBRc+0dUFnG+Fz1RHzGCRT+urndJQKHM/7u39HKsgH7PzC4WsfNLz1MjN/j0QA74HA9iIig0rx55jauNV0uQj7/try80mmIK308TzEwit80aGlK6YaJys7yFsoE/WqNuwkrg5J/p0dV3qZev8wGeTinAyaCnPdYgMwdU4oOZHb0kiHMLD9JZ0HTm83EqTGIeORkYGezXjnUP+NqQMm2xXwkz+CwB7kxVPqlglUtAFF/D+tEJu5v2MXqKHvC6e1dwuMKObsBEECgYEA9PlrTazd0KGT2qGVxMVwKuuvM/RRKrOKtrB7UpOfLbA3dIWUD/gclgYgN8Y4PhnObt5Y/Yo39eeTH8MV/I9JbfiqGM/2xhM3p0S+G7kqKSGUqBT1TOSuIRK1VGKHxn/Hd8RMqJDJTN6eqyMEyFU1wc2bUzqCKIR3K+S7RtB5sUkCgYEAr33ZPlqD3h7IqrwJUrNq1w/pgrF2l8YorWR+jhDb9diejzydIwx0oC8zolNdyWfZWryexJcEPodpqY+1A0tOVEGpZPy/pAqnUAQIk7ageIqA8PQYcggVAY5/ka1CrPafrqLI8HnSsIfAPReaiPOnTQG3dKCMM3QWrzawwooyvGkCgYBl+payxS2ptjmon++K9G4XT/mldSV80zUBjEIqRA3VpS/Qr/LNGwo7Yd1zmm67W17WRHVjRnC5S2expzK16qg0wXl9zsT5+/IFDdbeD5z/dl0+A0uCcQgT+IfH0ySWe4b70pne6jcCawI+V9ub0e0I+qCYaxjX6Rz7BsL3Xb3rEQKBgQCIRUcOXicLsgfMXFs49s091sNSkw8t59fqXKDRHFJRYzYsr7qtXpypTj3gDLBFFvj2toTqzwGitG/eFRNQTYezcZFiM8l0TjmBrsAiQ6v0LkjV0hWxZok5PjfdHRcrTA7PAuTZpx9tqNwkFFIRinIdH/oe/BQiWEDNKcC0L7AaEQKBgQDzLX7WoI785JZZbLDzDG413banIrRaixg10j/mVtpt7AE7gHui6eo7xFBUovRhL83NPjxb0bob9wSPZZfdRvCvHDUO1Nncg9W/31dqqkucGf85GjrShm9w5+faukTKGLYOZ1NOB6ZAVlUhAyBEWMxZaDSnrraaWabzbhPX36QsPw==',
//        ],
        //dlc_test
//        'alipay' => [
//            'client_id'     => '2021001192601329',
//            'client_secret' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlWgDzM16Q0DWztS+cccQV+ezBGZwzJzXYTHwE0x7aV2cVArYnKxYabalUG1PfQc4RqM7wea7qe+tjP625FrsXNn+5zI75UWavpBaTuzLiBDAv/F1/vwYE5xgu2qPf1O4Omxc4u5sEYyE4X4zIAydBGb0rMRVEC1kZXNeV6Tp1RLnlFMY0YzJ/Yoly5GOf8vQjSoQFzCmCG6AtH9cCTvCfAzvp1clIexESluxZE3PiNSonJlPh3upqzZojI6AOSSDFFystML84+cQw0h5JvxCFIctrJFdmN/AIgtZH0pN3TALGLAMiab6sTz7kZ0OGFF4unbH0eX/YBxRWGBB4+UtbwIDAQAB',
//            'redirect'      => 'https://apiuat.dlc.com.cn/member/sso/alipay',
//            'secret_key'=>'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCzBn9Y4tBJdShmZQ0zOs6HqBombl9YLqOLOHaXRMokpVQhJyA8jLO/itG9IiAVyziDp6mbCGlcs2MGhjKsKczTiIaqp+I2Uz59XqfL8vxAcFCD/SSDaI6IlWNPH827TGhMWboQBKpTo7ZEeC6p7oUkix5BofpJN3aPxh2OtL5V9b03DcD4czMysr2BEMkFIMyTb6wGhAEDBCqsNBBQ0CG/4zoo9bSdMPIO3Jtl0LmHKJhP6gv3TGrZw2bafvGvheswTowRG+8DRBkOPAzp07dSddAxtjvdTxrrYHu9rJppUvHrNekROOxU2ymiGnKtxdNiPTwQpketcOsgTMmnapOjAgMBAAECggEBAJRtjqfjZU2CFTzQC2GuIA3ZEdVLLUGvaWjEJ47DOdWoPVg/WDrbbSwhrENjR7bKBtEg8T3Ye4KgODnwiZK0FV5Mk1pqVuMzoVvQXs55ZysF87t65gsNZR9YvD2d4LSpZM/olJszQwSVd0jNW+MIHtttfGhuI1UQ5vvNBjSLJkOdVImjCDUrp22uUozb0CnmyFxJilAQl8A4XvxXLJ5muvxjERHaJ395fHH2Bf7UoJqKiwZkTSax6+D4P9W4p+ftnb2U4hDsWMBCGp/XeY0Wd+UKlTMbVNXkpcQW4RDepG64BAuu4KiB8qwL37nwOsRuAqELlWbLQip1cife/OmL9AECgYEA87aEk7LONO8NwvGC4XmeNFaLNSRivX7NmL0vJvcZqgzC3vbQL3smXAKTRGh4IZ5HDbZfqlEE3IJGP44/9oq2qiaKgoCBiniqiY8N+s/2ehjMOuZKLDo+fmfdcyFLQl5QprwWQDL7dUhfly7u5EMBDqTBxM0OeLYGznT5Gvwk3kkCgYEAvA0Wz/wgya2uUzvPzZYbO/Rwy8tjabtRQmHy7Z75SbOWfjnIYNQXRQhuKh0u1YBZEoKz5pKvMfv0Z8J7TGdYub+m8PZz5uTuxGgHDbUTyvdQW2lYucUrKqT/ZBbNpN7Z82vLBaEq8Y4l4bmnPMFq4QNe3Ywbr6fZizFtcNCe0osCgYA2EPiqd5FthtcLt15svPlO7SAm4vKQzW+5AcdTIqBr7DbTFfgrgGuLH/shwvdbpexna10fwKeOb8w7z0f7XtyVNMWnm/ChEpHqwHS+fqJUhenQ60PrOeShUFN9dhZnG9tSkglxpp3IcAzc0kmnFAJJCF4AVelu+BrcdHDK/sMDGQKBgCtlieIteRORtS0YHHiBL3/ChVVCdoqr47DCEeGsUjMj8pLmmstdppETxPiLC4fRurx+1S1cpHmQf3KCexnCr8MN62SoO08JfeBtcVNEenDYaf7ubL9SNQf+U8GxUXu+weVe9tcxvktIVo2k3mcy64tJz9aowqrivV1V5r+mJUDJAoGAZwIjksSodsdyEbPZVQQdi4sy8JoiyGsmEzMkYdOZSszKgKaFw9+mewxV+fJIApWcPIxHQ/b0e7cH143b9ByJNIBEpxFgljnkVhxQZ6sKWVBGjG5ev1SyG2bo86+5+2VbiOlO1qmj8flWzZCtAJDyL1NKUQeqAX3F0O6IxddZ+LQ=',
//        ],

        'qq' => [
            'client_id'     => 'your-app-id',
            'client_secret' => 'your-app-secret',
            'redirect'      => 'http://localhost/socialite/callback.php',
        ],



        'alipay' => [
            'client_id'     => '2021001192606370',
            'client_secret' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAhpLOQEhfhZEzgwyd5IgSNfwzEkuCBZ2T/vV0X15oCYD3XLoZqqLgdW1qGDaILQCPXiPfvJrQHGByfqRC+iOrpAVPyi7QlmJfXrqtLjuYzUJYwjswMhOaE1kHhGA/RMsyOfAx1uvBkecvGRJ2zjI0Gl7aD9JVbXQGgeafgddGbnBZcV5PAFboBGJEq9vWnW1RgF1CtYienzYTLxzqqEl/yKoQQvZSJye02ra//trjbnujBn02jd0Jy3r/I4WEPwngeowwlytelr9RxhgWpscm/pISf/Qp8uJCq+LO9rZslssOWktrv9pHBR/tUDIPDZUJUFGyFGyuyUwvClJI2CnXUwIDAQAB',
            'redirect'      => 'https://apiuat.dlc.com.cn/member/sso/alipay',
            'secret_key'=>'MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCGks5ASF+FkTODDJ3kiBI1/DMSS4IFnZP+9XRfXmgJgPdcuhmqouB1bWoYNogtAI9eI9+8mtAcYHJ+pEL6I6ukBU/KLtCWYl9euq0uO5jNQljCOzAyE5oTWQeEYD9EyzI58DHW68GR5y8ZEnbOMjQaXtoP0lVtdAaB5p+B10ZucFlxXk8AVugEYkSr29adbVGAXUK1iJ6fNhMvHOqoSX/IqhBC9lInJ7Tatr/+2uNue6MGfTaN3QnLev8jhYQ/CeB6jDCXK16Wv1HGGBamxyb+khJ/9Cny4kKr4s72tmyWyw5aS2u/2kcFH+1QMg8NlQlQUbIUbK7JTC8KUkjYKddTAgMBAAECggEAE3Kdyd5rp4LPXe3x13fuocybmNY+qQ8Xty3DrpCXGmB/3u+qC+XT6ERyo6Ml1NMS5PjsOXZqAt/RTAny//Etudtmp4JSdJkszSTPKDJGp8shxpgFJAd/KbKhCnlaS1fOH19d5IBXjf1J6ian8q6Apxr0CntMCzBMTHOGs53nuMfgANWWMZeDsgprjb+ksGlBmL08tV0Bm62lCHAdCOktxa+gmNMN2I3ePHy9NPzmmQBb0w8t1gJUysVB9ZJ5zTTm7Pq0aso1fPOdC1NXqFej4QjNugAY0+Y2+3F5CjfbocVPIhV7DiTGN2QkOs2qYMC9AnOlHfR1Loa2/VkhBBpqsQKBgQDCZRcZVU7W0Em+YPvy+O5u11QpqRsRrIg988t1mb0lPWYG1VQ3kUZxVufLeYCXcDmyQswEdqrFZymYL5aUaYAFeeYBnuIKyqyJJ8S3/oxkryDadCVuC7rsXq3TPNZlugVXl8kaUt1YLgZrGZYSBnSgBI3MMssvWOVnbnHdNzIYyQKBgQCxOIKX8aQ2B7yUc5pwCMcpv77A6jc/kee9HmtWz0rlWJnZW17V2+wN568699kvsg/ALjCXH4tILF1iOiwYaiE4H6mUGjEntQOeuUqBIPbF3CscISp6YfMCri2JeqesbjHWBdLq24SPJmVInTez36jrK12g6XQ4FKNJImax7V6ZOwKBgDF+Z4ktrSsIUR5FtyA/vQU/kdAhnCC92tpbLhw9Dmli3o0y14RmWpcU12N5BgKIskfutd8VD5m/EFoNE7upuMysIqGbAFZbD97D31QxXTFtXWIBXF5OYkM4P+2eeb/gwudwX+Vx36VJ4px2IT1po1vyjN/GtvDRI2dYOWlnWjvxAoGAYdlaubwwt6hT5f9iQQOHu7RkBS4MrSvJMPFPwNGZf8IIyCsb1Kal0hOe+8cHAr+k1K5sI7TF+WaNdQO7fwtyJgPNZLmFdHAMfKG/0kY6GoryvohNZN8aBtvWxraTR7BcEdiLWSM1MLi5aXts2wLdyGLcQlFD2wcCFQpKe2kdNysCgYA1UonblWOqS53k8rzGcUPROjgND5MiDRnWE5Y1n56ZB6+xT9fkPR4IIpShqHBTywDMml81PmXHoLQlqwZ27RYgSV3dIJiqkFrUmD6UDc5obFjzWyhDRgcQsWfG0JlAb+fK4f7p/ypKgizhS5QEFdhAgDdvyKSMleLEuL1FAYUETg==',
        ],

	];