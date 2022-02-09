<?php

use App\Models\OrganizationUserInvitation;

class OrganizationUserInvitationTest extends TestCase
{

//    /**
//     * @throws Exception
//     */
//    public function testInviteRegisteredUserToOrganizationReturnShouldMatchValue()
//    {
//        $user = \App\Models\User::where('email', 'jehan@ontelstudio.com')->first();
//
//        $this->be($user);
//
//        $user->setCurrentOrganization(18);
//
//        $request = new \Illuminate\Http\Request([
//            'email' => 'jehan+11@ontelstudio.com',
//            'role' => 'STAFF',
//            'password' => ''
//        ], ['GET']);
//
//        try {
//            $result = OrganizationUserInvitation::inst()->inviteUserToOrganization($request);
//
//            $this->assertInstanceOf(OrganizationUserInvitation::class, $result);
//            $this->assertEquals(18, $result->organization_id);
//
//        } catch (Exception $e) {
//            throw $e;
//        }
//    }

    /**
     * @throws Exception
     */
    public function testInviteNewUserToOrganizationReturnShouldMatchValue()
    {
        $user = \App\Models\User::where('email', 'jehan@ontelstudio.com')->first();

        $this->be($user);

        $user->setCurrentOrganization(1);

        $request = new \Illuminate\Http\Request([
            'email' => 'jehan+' . rand(1, 300) . '@ontelstudio.com',
            'role' => 'STAFF',
            'password' => ''
        ], ['GET']);

        try {
            $result = OrganizationUserInvitation::inst()->inviteUserToOrganization($request);

            $this->assertInstanceOf(OrganizationUserInvitation::class, $result);
            $this->assertEquals(1, $result->organization_id);

        } catch (Exception $e) {
            throw $e;
        }
    }
}