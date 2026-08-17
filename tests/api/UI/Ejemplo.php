<?php

final class Ejemplo
{
    public $translator;
    use DateBetweenCheckerTrait;

    public function inTempOrderValidationTicketWithinAccessControlToken(ApiTester $I): void
    {

        $I->wantTo('in temp an existing validationTicketId within access control token');

        $orderId = OrderFixture::ORDER_ID;
        $validationId = OrderFixture::VALIDATION_TICKET_VALIDATED_ID;
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(
            url: '/order/' . $orderId . '/validation-ticket/in-temp/' . $validationId,
        );

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    public function inTempNonExistingOrderWithValidationTicketCest(ApiTester $I): void
    {
        $I->wantTo('in temp non existing order with existing validation ticket');
        $I->haveCommonHttpHeaders(true);
        $I->loadFixtures(OrderFixture::class);

        $orderId = new OrderId();
        $validationId = OrderFixture::VALIDATION_TICKET_VALIDATED_ID;
        $I->sendPOST(
            url: '/order/' . $orderId . '/validation-ticket/in-temp/' . $validationId,
        );

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    public function inTempTomorrowOrderCest(ApiTester $I): void
    {
        $I->wantTo('in temp existing order with non existing validation ticket');
        $I->haveCommonHttpHeaders(true);
        $I->loadFixtures(TomorrowOrderFixture::class);

        $orderId = TomorrowOrderFixture::TOMORROW_ORDER_ID;
        $validationId = OrderFixture::VALIDATION_TICKET_ID;
        $I->sendPOST(
            url: '/order/' . $orderId . '/validation-ticket/in-temp/' . $validationId,
        );

        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
    }

    public function inTempModifiedOrderWithExistingValidationTicketCest(ApiTester $I): void
    {
        $I->wantTo('in temp  modified order');
        $I->haveCommonHttpHeaders(true);
        $I->loadFixtures(ModifiedOrderFixture::class);

        $orderId = ModifiedOrderFixture::MODIFIED_ORDER_ID;
        $validationId = OrderFixture::VALIDATION_TICKET_ID;
        $I->sendPOST(
            url: '/order/' . $orderId . '/validation-ticket/in-temp/' . $validationId,
        );

        $I->seeResponseCodeIs(HttpCode::CONFLICT);
    }

    public function inTempAnnulledOrderWithExistingValidationTicketCest(ApiTester $I): void
    {
        $I->wantTo('in temp anulled order');
        $I->haveCommonHttpHeaders(true);
        $I->loadFixtures(AnnulledOrderFixture::class);

        $orderId = AnnulledOrderFixture::ANULLED_ORDER_ID;
        $validationId = OrderFixture::VALIDATION_TICKET_ID;
        $I->sendPOST(
            url: '/order/' . $orderId . '/validation-ticket/in-temp/' . $validationId,
        );

        $I->seeResponseCodeIs(HttpCode::CONFLICT);
    }

    public function inTempAllTogetherOrderCest(ApiTester $I): void
    {
        $I->wantTo('in temp all together order');
        $I->haveCommonHttpHeaders(true);
        $I->loadFixtures(AllTogetherOrderFixture::class);

        $orderId = AllTogetherOrderFixture::ALL_TOGETHER_ORDER_ID;
        $validationId = OrderFixture::VALIDATION_TICKET_ID;
        $I->sendPOST(
            url: '/order/' . $orderId . '/validation-ticket/in-temp/' . $validationId,
        );

        $I->seeResponseCodeIs(HttpCode::CONFLICT);
        $I->seeResponseContainsJson(
            [
                'message' => $this->translator->trans(
                    id: 'TR_VALIDATION_TICKET_ALL_TOGETHER_ERROR',
                    parameters: [],
                    domain: 'messages',
                    locale: 'es'
                ),
            ]
        );
    }

    /**
     * @throws DatetimeException
     */
    public function inTemp1OrderValidationTicketCest(ApiTester $I): void
    {
        $I->wantTo('in temp of an existing order validation ticket and checks inTemp is setted');
        $I->haveCommonHttpHeaders(true);
        $I->loadFixtures(OrderFixture::class);

        $datetimeBefore = $this->oneMinuteAgo();
        $orderId = OrderFixture::ORDER_ID;
        $validationId = OrderFixture::VALIDATION_TICKET_OUT_TEMP_1_ID;
        $I->sendPOST(
            url: '/order/' . $orderId . '/validation-ticket/in-temp/' . $validationId,
        );
        $datetimeAfter = $this->oneMinuteAfter();

        $I->seeResponseCodeIs(HttpCode::OK);
        $valdiationTicket = $I->grabEntityFromRepository(ValidationTicket::class, [
            'id' => $validationId,
        ]);
        $this->assertDateIsBetween(
            dateTime: $valdiationTicket->inTemp1(),
            datetimeBefore: $datetimeBefore,
            datetimeAfter: $datetimeAfter
        );
    }

    /**
     * @throws DatetimeException
     */
    public function inTemp2OrderValidationTicketCest(ApiTester $I): void
    {
        $I->wantTo('in temp 2 of an existing order validation ticket and checks inTemp2 is setted');
        $I->haveCommonHttpHeaders(true);
        $I->loadFixtures(OrderFixture::class);

        $datetimeBefore = $this->oneMinuteAgo();
        $orderId = OrderFixture::ORDER_ID;
        $validationId = OrderFixture::VALIDATION_TICKET_OUT_TEMP_2_ID;
        $I->sendPOST(
            url: '/order/' . $orderId . '/validation-ticket/in-temp/' . $validationId,
        );
        $datetimeAfter = $this->oneMinuteAfter();

        $I->seeResponseCodeIs(HttpCode::OK);
        $valdiationTicket = $I->grabEntityFromRepository(ValidationTicket::class, [
            'id' => $validationId,
        ]);
        $this->assertDateIsBetween(
            dateTime: $valdiationTicket->inTemp2(),
            datetimeBefore: $datetimeBefore,
            datetimeAfter: $datetimeAfter
        );
    }

    /**
     * @throws DatetimeException
     */
    public function inTemp3OrderValidationTicketCest(ApiTester $I): void
    {
        $I->wantTo('in temp 3 of an existing order validation ticket and checks inTemp3 is setted');
        $I->haveCommonHttpHeaders(true);
        $I->loadFixtures(OrderFixture::class);

        $datetimeBefore = $this->oneMinuteAgo();
        $orderId = OrderFixture::ORDER_ID;
        $validationId = OrderFixture::VALIDATION_TICKET_OUT_TEMP_3_ID;
        $I->sendPOST(
            url: '/order/' . $orderId . '/validation-ticket/in-temp/' . $validationId,
        );
        $datetimeAfter = $this->oneMinuteAfter();

        $I->seeResponseCodeIs(HttpCode::OK);
        $valdiationTicket = $I->grabEntityFromRepository(ValidationTicket::class, [
            'id' => $validationId,
        ]);
        $this->assertDateIsBetween(
            dateTime: $valdiationTicket->inTemp3(),
            datetimeBefore: $datetimeBefore,
            datetimeAfter: $datetimeAfter
        );
    }

    public function inTemp3TwiceOrderValidationTicketCest(ApiTester $I): void
    {
        $I->wantTo('in temp 3 twice of an existing order validation ticket');
        $I->haveCommonHttpHeaders(true);
        $I->loadFixtures(OrderFixture::class);

        $orderId = OrderFixture::ORDER_ID;
        $validationId = OrderFixture::VALIDATION_TICKET_IN_TEMP_3_ID;
        $I->sendPOST(
            url: '/order/' . $orderId . '/validation-ticket/in-temp/' . $validationId,
        );

        $I->seeResponseCodeIs(HttpCode::CONFLICT);
        $I->seeResponseContainsJson(
            [
                'message' => $this->translator->trans(
                    id: 'TR_VALIDATION_TICKET_IN_TEMP3_ALREADY_ERROR',
                    parameters: [],
                    domain: 'messages',
                    locale: 'es'
                ),
            ]
        );
    }

    public function inTempWithoutOutTempOrderValidationTicketCest(ApiTester $I): void
    {
        $I->wantTo('in temp without out temp');
        $I->haveCommonHttpHeaders(true);
        $I->loadFixtures(OrderFixture::class);

        $orderId = OrderFixture::ORDER_ID;
        $validationId = OrderFixture::VALIDATION_TICKET_VALIDATED_ID;
        $I->sendPOST(
            url: '/order/' . $orderId . '/validation-ticket/in-temp/' . $validationId,
        );

        $I->seeResponseCodeIs(HttpCode::CONFLICT);
        $I->seeResponseContainsJson(
            [
                'message' => $this->translator->trans(
                    id: 'TR_VALIDATION_TICKET_NOT_OUT_TEMP_PREVIOUSLY_ERROR',
                    parameters: [],
                    domain: 'messages',
                    locale: 'es'
                ),
            ]
        );
    }

    /**
     * @throws DatetimeException
     */
    public function outTempAndInTempTicketCest(ApiTester $I): void
    {
        $I->wantTo('out temp and in temp');

        $I->haveCommonHttpHeaders(true);

        $I->loadFixtures(OrderFixture::class);

        $orderId = OrderFixture::ORDER_ID;
        $validationId = OrderFixture::VALIDATION_TICKET_OUT_TEMP_1_ID;

        $I->haveCommonHttpHeaders(true);

        $I->sendPOST(
            url: '/order/' . $orderId . '/validation-ticket/in-temp/' . $validationId,
        );
        $datetimeAfter = $this->oneMinuteAfter();

        $I->seeResponseCodeIs(HttpCode::OK);
        $validationTicket = $I->grabEntityFromRepository(ValidationTicket::class, [
            'id' => $validationId,
        ]);

        $this->assertDateIsBetween(
            dateTime: $validationTicket->inTemp1(),
            datetimeBefore: $validationTicket->outTemp1(),
            datetimeAfter: $datetimeAfter
        );
    }
}