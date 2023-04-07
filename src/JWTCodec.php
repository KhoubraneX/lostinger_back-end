<?php
    class JWTCodec {
        public function encode(array $payload) {
            $header = json_encode([
                "alg" => "HS256",
                "typ" => "JWT"
            ]);

            $header = $this->base64UrlEncode($header);

            $payload = $this->base64UrlEncode(json_encode($payload));

            $key = "73367639792F423F4528482B4D6251655468576D5A7134743777217A25432646";

            $signature  = hash_hmac("sha256" , $header . "." . $payload , $key , true);

            $signature = $this->base64UrlEncode($signature);

            return $header . "." . $payload . "." . $signature;
        }
        
        public function decode(string $token) {
            print_r(json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))));
        }

        private function base64UrlEncode(string $data): string {
            $base64Url = str_replace(["+" , "/" , "="] , ["-" , "_" , ""] , base64_encode($data));
            return $base64Url;
        }
    }
?>