<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Signing tests for the Crypt_GPG package.
 *
 * These tests require the PHPUnit 3.2 package to be installed. PHPUnit is
 * installable using PEAR. See the
 * {@link http://www.phpunit.de/pocket_guide/3.2/en/installation.html manual}
 * for detailed installation instructions.
 *
 * To run these tests, use:
 * <code>
 * $ phpunit SignTestCase
 * </code>
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation; either version 2.1 of the
 * License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @category  Encryption
 * @package   Crypt_GPG
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2005-2008 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Crypt_GPG
 */

/**
 * Base test case.
 */
require_once 'TestCase.php';

/**
 * Tests signing abilities of Crypt_GPG.
 *
 * @category  Encryption
 * @package   Crypt_GPG
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2005-2008 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/Crypt_GPG
 */
class SignTestCase extends TestCase
{
    // string
    // {{{ testSignKeyNotFoundException_invalid()

    /**
     * @expectedException Crypt_GPG_KeyNotFoundException
     *
     * @group string
     */
    public function testSignKeyNotFoundException_invalid()
    {
        $data = 'Hello, Alice! Goodbye, Bob!';
        $this->gpg->addSignKey('non-existent-key@example.com');
        $signedData = $this->gpg->sign($data);
    }

    // }}}
    // {{{ testSignKeyNotFoundException_none()

    /**
     * @expectedException Crypt_GPG_KeyNotFoundException
     *
     * @group string
     */
    public function testSignKeyNotFoundException_none()
    {
        $data = 'Hello, Alice! Goodbye, Bob!';
        $signedData = $this->gpg->sign($data);
    }

    // }}}
    // {{{ testSignBadPassphraseException_missing()

    /**
     * @expectedException Crypt_GPG_BadPassphraseException
     *
     * @group string
     */
    public function testSignBadPassphraseException_missing()
    {
        $data = 'Hello, Alice! Goodbye, Bob!';
        $this->gpg->addSignKey('first-keypair@example.com');
        $signedData = $this->gpg->sign($data);
    }

    // }}}
    // {{{ testSignBadPassphraseException_bad()

    /**
     * @expectedException Crypt_GPG_BadPassphraseException
     *
     * @group string
     */
    public function testSignBadPassphraseException_bad()
    {
        $data = 'Hello, Alice! Goodbye, Bob!';
        $this->gpg->addSignKey('first-keypair@example.com', 'incorrect');
        $signedData = $this->gpg->sign($data);
    }

    // }}}
    // {{{ testSignNoPassphrase()

    /**
     * @group string
     */
    public function testSignNoPassphrase()
    {
        $data = 'Hello, Alice! Goodbye, Bob!';
        $this->gpg->addSignKey('no-passphrase@example.com');
        $signedData = $this->gpg->sign($data);

        $signatures = $this->gpg->verify($signedData);
        $this->assertEquals(1, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
    // {{{ testSignNormal()

    /**
     * @group string
     */
    public function testSignNormal()
    {
        $data = 'Hello, Alice! Goodbye, Bob!';
        $this->gpg->addSignKey('first-keypair@example.com', 'test1');
        $signedData = $this->gpg->sign($data);

        $signatures = $this->gpg->verify($signedData);
        $this->assertEquals(1, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
    // {{{ testSignClear()

    /**
     * @group string
     */
    public function testSignClear()
    {
        $data = 'Hello, Alice! Goodbye, Bob!';
        $this->gpg->addSignKey('first-keypair@example.com', 'test1');
        $signedData = $this->gpg->sign($data, Crypt_GPG::SIGN_MODE_CLEAR);

        $signatures = $this->gpg->verify($signedData);
        $this->assertEquals(1, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
    // {{{ testSignDetached()

    /**
     * @group string
     */
    public function testSignDetached()
    {
        $data = 'Hello, Alice! Goodbye, Bob!';
        $this->gpg->addSignKey('first-keypair@example.com', 'test1');
        $signatureData = $this->gpg->sign($data,
            Crypt_GPG::SIGN_MODE_DETACHED);

        $signatures = $this->gpg->verify($data, $signatureData);
        $this->assertEquals(1, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
    // {{{ testSignDualOnePassphrase()

    /**
     * @group string
     */
    public function testSignDualOnePassphrase()
    {
        $data = 'Hello, Alice! Goodbye, Bob!';
        $this->gpg->addSignKey('no-passphrase@example.com');
        $this->gpg->addSignKey('first-keypair@example.com', 'test1');
        $signedData = $this->gpg->sign($data);

        $signatures = $this->gpg->verify($signedData);
        $this->assertEquals(2, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
    // {{{ testSignDualNormal()

    /**
     * @group string
     */
    public function testSignDualNormal()
    {
        $data = 'Hello, Alice! Goodbye, Bob!';
        $this->gpg->addSignKey('first-keypair@example.com', 'test1');
        $this->gpg->addSignKey('second-keypair@example.com', 'test2');
        $signedData = $this->gpg->sign($data);

        $signatures = $this->gpg->verify($signedData);
        $this->assertEquals(2, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
    // {{{ testSignDualClear()

    /**
     * @group string
     */
    public function testSignDualClear()
    {
        $data = 'Hello, Alice! Goodbye, Bob!';
        $this->gpg->addSignKey('first-keypair@example.com', 'test1');
        $this->gpg->addSignKey('second-keypair@example.com', 'test2');
        $signedData = $this->gpg->sign($data, Crypt_GPG::SIGN_MODE_CLEAR);

        $signatures = $this->gpg->verify($signedData);
        $this->assertEquals(2, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
    // {{{ testSignDualDetached()

    /**
     * @group string
     */
    public function testSignDualDetached()
    {
        $data = 'Hello, Alice! Goodbye, Bob!';
        $this->gpg->addSignKey('first-keypair@example.com', 'test1');
        $this->gpg->addSignKey('second-keypair@example.com', 'test2');
        $signatureData = $this->gpg->sign($data,
            Crypt_GPG::SIGN_MODE_DETACHED);

        $signatures = $this->gpg->verify($data, $signatureData);
        $this->assertEquals(2, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
    // {{{ testSignEmpty()

    /**
     * @group string
     */
    public function testSignEmpty()
    {
        $data = '';

        $this->gpg->addSignKey('first-keypair@example.com', 'test1');

        $signedData = $this->gpg->sign($data);
        $signatures = $this->gpg->verify($signedData);

        $this->assertEquals(1, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}

    // file
    // {{{ testSignFileNoPassphrase()

    /**
     * @group file
     */
    public function testSignFileNoPassphrase()
    {
        $inputFilename  = TestCase::DATADIR . '/testFileMedium.plain';
        $outputFilename = TestCase::TEMPDIR . '/testSignFileNoPassphrase.asc';

        $this->gpg->addSignKey('no-passphrase@example.com');
        $this->gpg->signFile($inputFilename, $outputFilename);

        $signatures = $this->gpg->verifyFile($outputFilename);
        $this->assertEquals(1, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
    // {{{ testSignFileNormal()

    /**
     * @group file
     */
    public function testSignFileNormal()
    {
        $inputFilename  = TestCase::DATADIR . '/testFileMedium.plain';
        $outputFilename = TestCase::TEMPDIR . '/testSignFileNormal.asc';

        $this->gpg->addSignKey('first-keypair@example.com', 'test1');
        $this->gpg->signFile($inputFilename, $outputFilename);

        $signatures = $this->gpg->verifyFile($outputFilename);
        $this->assertEquals(1, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
    // {{{ testSignFileClear()

    /**
     * @group file
     */
    public function testSignFileClear()
    {
        $inputFilename  = TestCase::DATADIR . '/testFileMedium.plain';
        $outputFilename = TestCase::TEMPDIR . '/testSignFileClear.asc';

        $this->gpg->addSignKey('first-keypair@example.com', 'test1');
        $this->gpg->signFile($inputFilename, $outputFilename,
            Crypt_GPG::SIGN_MODE_CLEAR);

        $signatures = $this->gpg->verifyFile($outputFilename);
        $this->assertEquals(1, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
    // {{{ testSignFileDetached()

    /**
     * @group file
     */
    public function testSignFileDetached()
    {
        $inputFilename  = TestCase::DATADIR . '/testFileMedium.plain';
        $outputFilename = TestCase::TEMPDIR . '/testSignFileDetached.asc';

        $this->gpg->addSignKey('first-keypair@example.com', 'test1');
        $this->gpg->signFile($inputFilename, $outputFilename,
            Crypt_GPG::SIGN_MODE_DETACHED);

        $signatureData = file_get_contents($outputFilename);

        $signatures = $this->gpg->verifyFile($inputFilename, $signatureData);
        $this->assertEquals(1, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
    // {{{ testSignFileDetachedToString()

    /**
     * @group file
     */
    public function testSignFileDetachedToString()
    {
        $filename = TestCase::DATADIR . '/testFileMedium.plain';

        $this->gpg->addSignKey('first-keypair@example.com', 'test1');
        $signatureData = $this->gpg->signFile($filename, null,
            Crypt_GPG::SIGN_MODE_DETACHED);

        $signatures = $this->gpg->verifyFile($filename, $signatureData);
        $this->assertEquals(1, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
    // {{{ testSignFileDualOnePassphrase()

    /**
     * @group file
     */
    public function testSignFileDualOnePassphrase()
    {
        $inputFilename  = TestCase::DATADIR . '/testFileMedium.plain';
        $outputFilename = TestCase::TEMPDIR .
            '/testSignFileDualOnePassphrase.asc';

        $this->gpg->addSignKey('no-passphrase@example.com');
        $this->gpg->addSignKey('first-keypair@example.com', 'test1');
        $this->gpg->signFile($inputFilename, $outputFilename);

        $signatures = $this->gpg->verifyFile($outputFilename);
        $this->assertEquals(2, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
    // {{{ testSignFileDualNormal()

    /**
     * @group file
     */
    public function testSignFileDualNormal()
    {
        $inputFilename  = TestCase::DATADIR . '/testFileMedium.plain';
        $outputFilename = TestCase::TEMPDIR . '/testSignFileDualNormal.asc';

        $this->gpg->addSignKey('first-keypair@example.com', 'test1');
        $this->gpg->addSignKey('second-keypair@example.com', 'test2');
        $this->gpg->signFile($inputFilename, $outputFilename);

        $signatures = $this->gpg->verifyFile($outputFilename);
        $this->assertEquals(2, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
    // {{{ testSignFileDualClear()

    /**
     * @group file
     */
    public function testSignFileDualClear()
    {
        $inputFilename  = TestCase::DATADIR . '/testFileMedium.plain';
        $outputFilename = TestCase::TEMPDIR . '/testSignFileDualClear.asc';

        $this->gpg->addSignKey('first-keypair@example.com', 'test1');
        $this->gpg->addSignKey('second-keypair@example.com', 'test2');
        $this->gpg->signFile($inputFilename, $outputFilename,
            Crypt_GPG::SIGN_MODE_CLEAR);

        $signatures = $this->gpg->verifyFile($outputFilename);
        $this->assertEquals(2, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
    // {{{ testSignFileDualDetached()

    /**
     * @group file
     */
    public function testSignFileDualDetached()
    {
        $inputFilename  = TestCase::DATADIR . '/testFileMedium.plain';
        $outputFilename = TestCase::TEMPDIR . '/testSignFileDualDetached.asc';

        $this->gpg->addSignKey('first-keypair@example.com', 'test1');
        $this->gpg->addSignKey('second-keypair@example.com', 'test2');
        $this->gpg->signFile($inputFilename, $outputFilename,
            Crypt_GPG::SIGN_MODE_DETACHED);

        $signatureData = file_get_contents($outputFilename);

        $signatures = $this->gpg->verifyFile($inputFilename, $signatureData);
        $this->assertEquals(2, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
    // {{{ testSignFileFileException_input()

    /**
     * @expectedException Crypt_GPG_FileException
     *
     * @group file
     */
    public function testSignFileFileException_input()
    {
        // input file does not exist
        $inputFilename = TestCase::DATADIR .
            '/testSignFileFileFileException_input.plain';

        $this->gpg->addSignKey('first-keypair@example.com', 'test1');
        $this->gpg->signFile($inputFilename);
    }

    // }}}
    // {{{ testSignFileFileException_output()

    /**
     * @expectedException Crypt_GPG_FileException
     *
     * @group file
     */
    public function testSignFileFileException_output()
    {
        // input file is encrypted with first-keypair@example.com
        // output file does not exist
        $inputFilename  = TestCase::DATADIR . '/testMediumFile.plain';
        $outputFilename = './non-existent' .
            '/testSignFileFileException_output.plain';

        $this->gpg->addSignKey('first-keypair@example.com', 'test1');
        $this->gpg->signFile($inputFilename, $outputFilename);
    }

    // }}}
    // {{{ testSignFileEmpty()

    /**
     * @group file
     */
    public function testSignFileEmpty()
    {
        $filename = TestCase::DATADIR . '/testFileEmpty.plain';

        $this->gpg->addSignKey('first-keypair@example.com', 'test1');

        $signedData = $this->gpg->signFile($filename);
        $signatures = $this->gpg->verify($signedData);

        $this->assertEquals(1, count($signatures));
        foreach ($signatures as $signature) {
            $this->assertTrue($signature->isValid());
        }
    }

    // }}}
}

?>
