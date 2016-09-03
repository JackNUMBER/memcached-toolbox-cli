<?php
/**
 * by Antoine Cadoret (@jacknumber)
 */

class CacheToolbox extends Memcached
{
    private $host = 'localhost';
    private $port = 11211;

    public function run()
    {
        // take arguments
        global $argv;

        $this->addServer($this->host, $this->port);

        $key = (empty($argv[2])) ? '' : $argv[2];
        $data = (empty($argv[3])) ? '' : $argv[3];

        switch ($argv[1]) {

            case 'help':
                $this->_help();
                break;

            case 'flush':
                $this->flush();
                break;

            case 'get':
                if (!$key) {
                    print 'key missing' . "\n";
                    break;
                }
                print_r($this->get($key));
                print "\n";
                break;

            case 'getAllKeys':
                $this->_getAllKeys();
                break;

            case 'delete':
                if (!$key) {
                    print 'key missing' . "\n";
                    break;
                }
                $this->delete($key);
                if ($this->get($key)) {
                    print '/!\ ' . $key . ' not deleted' . "\n";
                }
                break;

            case 'deleteAllKeys':
                $keys = $this->getAllKeys();

                foreach ($keys as $key) {
                    $this->delete($key);
                    if ($this->get($key)) {
                        print '/!\ ' . $key . ' not deleted' . "\n";
                    }
                }
                break;

            case 'set':
                if (!$key) {
                    print 'key missing' . "\n";
                    break;
                }
                $this->set($key, $data);
                print 'Stored: '. $key . ' => ';
                print_r($this->get($key));
                print "\n";
                break;

            case 'increment':
                if (!$key) {
                    print 'key missing' . "\n";
                    break;
                }
                $this->increment($key);
                print 'New value: ' . $this->get($key) . "\n";
                break;

            case 'decrement':
                if (!$key) {
                    print 'key missing' . "\n";
                    break;
                }
                $this->decrement($key);
                print 'New value: ' . $this->get($key) . "\n";
                break;

            case 'stats':
                print_r($this->getStats());
                print "\n";
                break;

            default:
                print 'Unknown command. Use "help" ;)' . "\n";
                break;
        }
    }

    private function _help() {
        // echo "stats" | nc -w 1 localhost 11211 | awk '$2 == "bytes" { print $2" "$3 }'
        $help = [
            'flush'             => 'Invalidate all keys',
            'get <key>'         => 'Return the key value',
            'getAllKeys'        => 'List keys on the server',
            'delete <key>'      => 'Delete the key',
            'deleteAllKeys'     => 'Delete all the key',
            'set <key> <value>' => 'Set the key with the value',
            'increment <key>'   => 'Add 1 to the key',
            'decrement <key>'   => 'Remove 1 to the key',
            'stats'             => 'Show some stats about Memcached',
        ];

        print "\n\n";
        print '==  Help of cache toolbox  ==' . "\n";
        print "\n\n";

        print 'Engine:' . "\t" . 'memcached' . "\n";
        print 'Host: ' . "\t" . $this->host . "\n";
        print 'Port: ' . "\t" . $this->port . "\n";

        print "\n";

        print 'Commands:' . "\n";

        foreach ($help as $command => $desc) {
            $separator = "\t";

            if (strlen($command) < 15) {
                $separator .= "\t";
            }

            if (strlen($command) < 7) {
                $separator .= "\t";
            }

            print $command . $separator . $desc . "\n";
        }

    }

    private function _getAllKeys() {
        $keys = $this->getAllKeys();

        if ($keys) {
            foreach ($keys as $key => $value) {
                print $value . "\n";
            }
        } else {
            // no keys
            print 'no keys' . "\n";
        }
    }
}

if (php_sapi_name() == 'cli') {
    $obj = new CacheToolbox();
    echo $obj->run();
}
