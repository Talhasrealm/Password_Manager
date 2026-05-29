<?php
class PasswordGenerator {
    private string $upper   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private string $lower   = 'abcdefghijklmnopqrstuvwxyz';
    private string $numbers = '0123456789';
    private string $special = '!@#$%^&*()_+-=[]{}';

    public function generate(int $length, int $upper, int $lower, int $numbers, int $special): string {
        $password = '';
        $password .= $this->pick($this->upper,   $upper);
        $password .= $this->pick($this->lower,   $lower);
        $password .= $this->pick($this->numbers, $numbers);
        $password .= $this->pick($this->special, $special);

        // Fill remaining characters randomly
        $all = $this->upper . $this->lower . $this->numbers . $this->special;
        while (strlen($password) < $length) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($password);
    }

    private function pick(string $chars, int $count): string {
        $result = '';
        for ($i = 0; $i < $count; $i++) {
            $result .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $result;
    }
}